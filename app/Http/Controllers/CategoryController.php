<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('items')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', 'unique:categories,code'],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::create($validated);
        AuditLog::record('create_category', 'Category', $category->id, null, $category->toArray());

        return back()->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', Rule::unique('categories', 'code')->ignore($category->id)],
            'description' => ['nullable', 'string'],
        ]);

        $old = $category->toArray();
        $category->update($validated);
        AuditLog::record('update_category', 'Category', $category->id, $old, $category->toArray());

        return back()->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroy(Category $category)
    {
        if ($category->items()->exists()) {
            return back()->withErrors(['category' => "Kategori '{$category->name}' masih memiliki relasi barang dan tidak dapat dihapus."]);
        }

        $name = $category->name;
        $category->delete();
        AuditLog::record('delete_category', 'Category', $category->id, ['name' => $name]);

        return back()->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
