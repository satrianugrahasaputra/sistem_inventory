@extends('layouts.app')

@section('title', 'Ubah Data Barang: ' . $item->name)
@section('page_title', 'Ubah Data Barang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Breadcrumb Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('items.show', $item) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Detail Barang
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Ubah Master Barang: {{ $item->name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5 font-mono">SKU: {{ $item->sku }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $item->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>

        <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Identitas Barang -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">1. Identitas Barang</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="sku" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kode SKU / Part Number <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $item->sku) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Barang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="unit_id" class="block text-xs font-semibold text-slate-700 mb-1">
                            Satuan Ukur <span class="text-rose-500">*</span>
                        </label>
                        <select name="unit_id" id="unit_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Penempatan Gudang & Batas Stok -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">2. Penempatan Gudang & Stok</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="sm:col-span-2">
                        <label for="warehouse_id" class="block text-xs font-semibold text-slate-700 mb-1">Gudang Default</label>
                        <select name="warehouse_id" id="warehouse_id"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            <option value="">-- Tanpa Gudang Khusus --</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id', $item->warehouse_id) == $wh->id ? 'selected' : '' }}>{{ $wh->name }} ({{ $wh->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="min_stock" class="block text-xs font-semibold text-slate-700 mb-1">
                            Stok Minimum <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', $item->min_stock) }}" min="0" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Stok Saat Ini (Terkunci)</label>
                        <input type="text" disabled value="{{ $item->current_stock }} {{ $item->unit->symbol ?? '' }}"
                            class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-extrabold text-slate-600 cursor-not-allowed">
                        <p class="text-[10px] text-slate-400 mt-1">Hanya berubah lewat transaksi masuk/keluar</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Harga -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">3. Harga Estimasi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="purchase_price" class="block text-xs font-semibold text-slate-700 mb-1">Harga Beli Standar (Rp)</label>
                        <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}" min="0" step="100"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label for="selling_price" class="block text-xs font-semibold text-slate-700 mb-1">Harga Jual / Nilai Keluar (Rp)</label>
                        <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $item->selling_price) }}" min="0" step="100"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 4: Deskripsi & Status Aktif -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">4. Keterangan & Status</h3>
                <div class="space-y-4">
                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Spesifikasi Teknis</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">{{ old('description', $item->description) }}</textarea>
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center text-xs text-slate-700 font-semibold cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            <span class="ml-2">Barang Aktif (Dapat dipilih dalam transaksi masuk dan keluar)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end space-x-3">
                <a href="{{ route('items.show', $item) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm shadow-indigo-600/30 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
