<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['locations'])->withCount(['items'])->orderBy('name')->get();
        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', 'unique:warehouses,code'],
            'address' => ['nullable', 'string'],
        ]);

        $warehouse = Warehouse::create($validated);
        AuditLog::record('create_warehouse', 'Warehouse', $warehouse->id, null, $warehouse->toArray());

        return back()->with('success', "Gudang '{$warehouse->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', Rule::unique('warehouses', 'code')->ignore($warehouse->id)],
            'address' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $old = $warehouse->toArray();
        $warehouse->update($validated);
        AuditLog::record('update_warehouse', 'Warehouse', $warehouse->id, $old, $warehouse->toArray());

        return back()->with('success', "Gudang '{$warehouse->name}' berhasil diperbarui.");
    }

    public function storeLocation(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('locations', 'code')->where(fn ($q) => $q->where('warehouse_id', $warehouse->id)),
            ],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $location = $warehouse->locations()->create($validated);
        AuditLog::record('create_location', 'Location', $location->id, null, $location->toArray());

        return back()->with('success', "Rak/Lokasi '{$location->code}' berhasil ditambahkan ke {$warehouse->name}.");
    }

    public function destroyLocation(Location $location)
    {
        if ($location->items()->exists()) {
            return back()->withErrors(['location' => "Lokasi '{$location->code}' masih berisi barang dan tidak dapat dihapus."]);
        }

        $code = $location->code;
        $location->delete();
        AuditLog::record('delete_location', 'Location', $location->id, ['code' => $code]);

        return back()->with('success', "Lokasi '{$code}' berhasil dihapus.");
    }
}
