<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount('items')->orderBy('name')->get();
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'symbol' => ['required', 'string', 'max:20', 'unique:units,symbol'],
        ]);

        $unit = Unit::create($validated);
        AuditLog::record('create_unit', 'Unit', $unit->id, null, $unit->toArray());

        return back()->with('success', "Satuan '{$unit->name} ({$unit->symbol})' berhasil ditambahkan.");
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'symbol' => ['required', 'string', 'max:20', Rule::unique('units', 'symbol')->ignore($unit->id)],
        ]);

        $old = $unit->toArray();
        $unit->update($validated);
        AuditLog::record('update_unit', 'Unit', $unit->id, $old, $unit->toArray());

        return back()->with('success', "Satuan '{$unit->name}' berhasil diperbarui.");
    }

    public function destroy(Unit $unit)
    {
        if ($unit->items()->exists()) {
            return back()->withErrors(['unit' => "Satuan '{$unit->name}' masih dipakai pada data barang dan tidak dapat dihapus."]);
        }

        $name = $unit->name;
        $unit->delete();
        AuditLog::record('delete_unit', 'Unit', $unit->id, ['name' => $name]);

        return back()->with('success', "Satuan '{$name}' berhasil dihapus.");
    }
}
