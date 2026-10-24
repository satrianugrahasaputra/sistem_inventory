<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $opnames = StockOpname::with(['warehouse', 'user', 'approvedByUser'])
            ->latest('date')
            ->latest('id')
            ->paginate(10);

        return view('stock_opnames.index', compact('opnames'));
    }

    public function create(InventoryService $service)
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $items = Item::where('is_active', true)->with(['unit', 'category', 'location'])->orderBy('name')->get();
        $suggestedDocNumber = $service->generateDocumentNumber('OPN');

        return view('stock_opnames.create', compact('warehouses', 'items', 'suggestedDocNumber'));
    }

    public function store(Request $request, InventoryService $service)
    {
        $validated = $request->validate([
            'document_number' => ['nullable', 'string', 'max:50', 'unique:stock_opnames,document_number'],
            'date' => ['required', 'date'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.physical_stock' => ['required', 'integer', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $opname = DB::transaction(function () use ($validated, $service) {
            $docNumber = $validated['document_number'] ?? $service->generateDocumentNumber('OPN');

            $opname = StockOpname::create([
                'document_number' => $docNumber,
                'date' => $validated['date'],
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'user_id' => auth()->id(),
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $entry) {
                $item = Item::findOrFail($entry['item_id']);
                $systemStock = $item->current_stock;
                $physicalStock = intval($entry['physical_stock']);
                $diff = $physicalStock - $systemStock;

                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'item_id' => $item->id,
                    'system_stock' => $systemStock,
                    'physical_stock' => $physicalStock,
                    'difference' => $diff,
                    'notes' => $entry['notes'] ?? null,
                ]);
            }

            return $opname;
        });

        return redirect()->route('stock-opnames.show', $opname)
            ->with('success', "Draft sesi Stock Opname {$opname->document_number} berhasil dibuat dan menunggu persetujuan Manajer.");
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['warehouse', 'user', 'approvedByUser', 'details.item.unit']);
        return view('stock_opnames.show', compact('stockOpname'));
    }

    public function approve(StockOpname $stockOpname, InventoryService $service)
    {
        $service->approveStockOpname($stockOpname, auth()->user());

        return redirect()->route('stock-opnames.show', $stockOpname)
            ->with('success', "Stock Opname {$stockOpname->document_number} berhasil disetujui. Stok gudang telah disesuaikan dan tercatat di kartu stok.");
    }
}
