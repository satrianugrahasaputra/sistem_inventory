@extends('layouts.app')

@section('title', 'Tambah Barang Baru')
@section('page_title', 'Tambah Barang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Breadcrumb Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('items.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Data Barang
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-900">Formulir Barang Baru</h2>
            <p class="text-xs text-slate-500 mt-0.5">Lengkapi informasi master data barang untuk pencatatan inventaris.</p>
        </div>

        <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Section 1: Identitas Barang -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">1. Identitas Barang</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kode SKU / Part Number <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required placeholder="Contoh: ELK-SENS-001"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <p class="text-[10px] text-slate-400 mt-1">Harus unik di sistem. Contoh format: [KAT]-[KODE]-[NO]</p>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Barang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Sensor Proximity Induktif M12"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit_id" class="block text-xs font-semibold text-slate-700 mb-1">
                            Satuan Ukur <span class="text-rose-500">*</span>
                        </label>
                        <select name="unit_id" id="unit_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Penempatan Gudang & Batas Stok -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">2. Penempatan Gudang & Stok</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Warehouse -->
                    <div class="sm:col-span-2">
                        <label for="warehouse_id" class="block text-xs font-semibold text-slate-700 mb-1">Gudang Penyimpanan</label>
                        <select name="warehouse_id" id="warehouse_id"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            <option value="">-- Pilih Gudang Default --</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }} ({{ $wh->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Min Stock Threshold -->
                    <div>
                        <label for="min_stock" class="block text-xs font-semibold text-slate-700 mb-1">
                            Stok Minimum <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 5) }}" min="0" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <p class="text-[10px] text-slate-400 mt-1">Peringatan muncul jika stok &le; angka ini</p>
                    </div>

                    <!-- Initial Stock (If any) -->
                    <div>
                        <label for="initial_stock" class="block text-xs font-semibold text-slate-700 mb-1">Saldo Awal Fisik</label>
                        <input type="number" name="initial_stock" id="initial_stock" value="{{ old('initial_stock', 0) }}" min="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <p class="text-[10px] text-slate-400 mt-1">Otomatis dicatat di mutasi kartu stok</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Finansial & Harga -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">3. Harga Estimasi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="purchase_price" class="block text-xs font-semibold text-slate-700 mb-1">Harga Beli Standar (Rp)</label>
                        <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" min="0" step="100"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label for="selling_price" class="block text-xs font-semibold text-slate-700 mb-1">Harga Jual / Nilai Keluar (Rp)</label>
                        <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" min="0" step="100"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 4: Deskripsi & Dokumen -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">4. Keterangan Tambahan</h3>
                <div class="space-y-4">
                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Spesifikasi Teknis</label>
                        <textarea name="description" id="description" rows="3" placeholder="Informasi detail mengenai spesifikasi teknis barang..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end space-x-3">
                <a href="{{ route('items.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" id="btn-save-item" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm shadow-indigo-600/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Barang Baru
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
