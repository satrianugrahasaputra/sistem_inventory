<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $query = StockIn::with(['supplier', 'warehouse', 'user'])->latest('date')->latest('id');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
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

        $stockIns = $query->paginate(10)->withQueryString();

        return view('stock_ins.index', compact('stockIns'));
    }

    public function create(InventoryService $service)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->with('locations')->orderBy('name')->get();
        $items = Item::where('is_active', true)->with(['unit', 'location'])->orderBy('name')->get();
        $suggestedDocNumber = $service->generateDocumentNumber('IN');

        return view('stock_ins.create', compact('suppliers', 'warehouses', 'items', 'suggestedDocNumber'));
    }

    public function store(Request $request, InventoryService $service)
    {
        $validated = $request->validate([
            'document_number' => ['nullable', 'string', 'max:50', 'unique:stock_ins,document_number'],
            'date' => ['required', 'date'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $stockIn = $service->recordStockIn(
            [
                'document_number' => $validated['document_number'] ?? null,
                'date' => $validated['date'],
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ],
            $validated['items'],
            auth()->user()
        );

        return redirect()->route('stock-ins.show', $stockIn)
            ->with('success', "Transaksi Barang Masuk {$stockIn->document_number} berhasil dicatat dan stok telah bertambah.");
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['supplier', 'warehouse', 'user', 'cancelledByUser', 'details.item.unit', 'details.location']);
        return view('stock_ins.show', compact('stockIn'));
    }

    public function void(Request $request, StockIn $stockIn, InventoryService $service)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $service->voidStockIn($stockIn, $request->input('reason'), auth()->user());

        return redirect()->route('stock-ins.show', $stockIn)
            ->with('success', "Transaksi {$stockIn->document_number} berhasil dibatalkan dan stok telah dikoreksi kembali.");
    }
}
