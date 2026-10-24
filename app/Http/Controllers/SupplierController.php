<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('stockIns');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $supplier = Supplier::create($validated);
        AuditLog::record('create_supplier', 'Supplier', $supplier->id, null, $supplier->toArray());

        return back()->with('success', "Supplier '{$supplier->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $old = $supplier->toArray();
        $supplier->update($validated);
        AuditLog::record('update_supplier', 'Supplier', $supplier->id, $old, $supplier->toArray());

        return back()->with('success', "Supplier '{$supplier->name}' berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->stockIns()->exists()) {
            return back()->withErrors(['supplier' => "Supplier '{$supplier->name}' memiliki riwayat transaksi barang masuk sehingga tidak dapat dihapus."]);
        }

        $name = $supplier->name;
        $supplier->delete();
        AuditLog::record('delete_supplier', 'Supplier', $supplier->id, ['name' => $name]);

        return back()->with('success', "Supplier '{$name}' berhasil dihapus.");
    }
}
