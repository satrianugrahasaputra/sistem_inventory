<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit', 'warehouse', 'location']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->input('status')) {
            if ($status === 'out') {
                $query->where('current_stock', '<=', 0);
            } elseif ($status === 'low') {
                $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0);
            } elseif ($status === 'normal') {
                $query->whereColumn('current_stock', '>', 'min_stock');
            }
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $items = $query->latest('id')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->with('locations')->get();

        return view('items.create', compact('categories', 'units', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:64', 'unique:items,sku'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'initial_stock' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        $initialStock = intval($validated['initial_stock'] ?? 0);

        $item = DB::transaction(function () use ($validated, $imagePath, $initialStock) {
            $item = Item::create([
                'sku' => strtoupper($validated['sku']),
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'unit_id' => $validated['unit_id'],
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'location_id' => $validated['location_id'] ?? null,
                'purchase_price' => $validated['purchase_price'] ?? 0,
                'selling_price' => $validated['selling_price'] ?? 0,
                'current_stock' => $initialStock,
                'min_stock' => $validated['min_stock'] ?? 5,
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,
                'is_active' => true,
            ]);

            if ($initialStock > 0) {
                StockMovement::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $item->warehouse_id,
                    'user_id' => auth()->id(),
                    'type' => 'adjustment',
                    'quantity' => $initialStock,
                    'balance_before' => 0,
                    'balance_after' => $initialStock,
                    'reference_type' => Item::class,
                    'reference_id' => $item->id,
                    'reference_number' => 'SALDO-AWAL',
                    'notes' => 'Saldo awal pencatatan barang',
                    'created_at' => now(),
                ]);
            }

            AuditLog::record('create_item', 'Item', $item->id, null, $item->toArray());

            return $item;
        });

        return redirect()->route('items.show', $item)
            ->with('success', "Barang '{$item->name}' berhasil ditambahkan ke inventaris.");
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'warehouse', 'location']);
        $movements = $item->movements()->with(['user', 'warehouse'])->take(15)->get();

        return view('items.show', compact('item', 'movements'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->with('locations')->get();

        return view('items.edit', compact('item', 'categories', 'units', 'warehouses'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:64', Rule::unique('items', 'sku')->ignore($item->id)],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $validated['sku'] = strtoupper($validated['sku']);
        $oldData = $item->toArray();

        $item->update($validated);

        AuditLog::record('update_item', 'Item', $item->id, $oldData, $item->toArray());

        return redirect()->route('items.show', $item)
            ->with('success', "Informasi barang '{$item->name}' berhasil diperbarui.");
    }

    public function toggleActive(Item $item)
    {
        $oldStatus = $item->is_active;
        $item->is_active = !$oldStatus;
        $item->save();

        $action = $item->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::record('toggle_status', 'Item', $item->id, ['is_active' => $oldStatus], ['is_active' => $item->is_active]);

        return back()->with('success', "Barang '{$item->name}' berhasil {$action}.");
    }

    public function destroy(Item $item)
    {
        // ATURAN BISNIS #5: Barang dengan riwayat transaksi tidak dapat dihapus, hanya dinonaktifkan
        if ($item->hasTransactions()) {
            return back()->withErrors([
                'delete' => "Barang '{$item->name}' tidak dapat dihapus karena sudah memiliki riwayat mutasi/transaksi. Gunakan opsi 'Nonaktifkan' untuk menyembunyikannya.",
            ]);
        }

        $itemName = $item->name;
        $item->delete();

        AuditLog::record('delete_item', 'Item', $item->id, ['name' => $itemName]);

        return redirect()->route('items.index')
            ->with('success', "Barang '{$itemName}' berhasil dihapus dari sistem.");
    }
}
