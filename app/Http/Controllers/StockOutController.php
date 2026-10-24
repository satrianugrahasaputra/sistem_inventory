<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOut;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOut::with(['warehouse', 'user'])->latest('date')->latest('id');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhere('recipient', 'like', "%{$search}%");
            });
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('date', '<=', $dateTo);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $stockOuts = $query->paginate(10)->withQueryString();

        return view('stock_outs.index', compact('stockOuts'));
    }

    public function create(InventoryService $service)
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        // Hanya tampilkan barang aktif yang stoknya > 0
        $items = Item::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->with('unit')
            ->orderBy('name')
            ->get();
        $suggestedDocNumber = $service->generateDocumentNumber('OUT');

        return view('stock_outs.create', compact('warehouses', 'items', 'suggestedDocNumber'));
    }

    public function store(Request $request, InventoryService $service)
    {
        $validated = $request->validate([
            'document_number' => ['nullable', 'string', 'max:50', 'unique:stock_outs,document_number'],
            'date' => ['required', 'date'],
            'recipient' => ['required', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $stockOut = $service->recordStockOut(
            [
                'document_number' => $validated['document_number'] ?? null,
                'date' => $validated['date'],
                'recipient' => $validated['recipient'],
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ],
            $validated['items'],
            auth()->user()
        );

        return redirect()->route('stock-outs.show', $stockOut)
            ->with('success', "Transaksi Barang Keluar {$stockOut->document_number} berhasil diproses dan stok telah berkurang.");
    }

    public function show(StockOut $stockOut)
    {
        $stockOut->load(['warehouse', 'user', 'cancelledByUser', 'details.item.unit']);
        return view('stock_outs.show', compact('stockOut'));
    }

    public function void(Request $request, StockOut $stockOut, InventoryService $service)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $service->voidStockOut($stockOut, $request->input('reason'), auth()->user());

        return redirect()->route('stock-outs.show', $stockOut)
            ->with('success', "Transaksi {$stockOut->document_number} berhasil dibatalkan dan stok telah dikembalikan ke gudang.");
    }
}
