@extends('layouts.app')

@section('title', 'Master Data Barang')
@section('page_title', 'Data Barang')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Search Filter Card -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Katalog Data Barang</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh master barang, pemantauan stok minimum, dan SKU</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.stock') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Laporan Stok
            </a>
            <a href="{{ route('items.create') }}" id="btn-add-item" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs shadow-indigo-600/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Barang Baru
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('items.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <!-- Search Input (SKU / Name) -->
            <div class="lg:col-span-4">
                <label for="search" class="block text-[11px] font-semibold text-slate-500 mb-1">Cari Nama atau SKU</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Contoh: Sensor, BOR-001..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="lg:col-span-3">
                <label for="category_id" class="block text-[11px] font-semibold text-slate-500 mb-1">Kategori</label>
                <select name="category_id" id="category_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Status Filter -->
            <div class="lg:col-span-3">
                <label for="status" class="block text-[11px] font-semibold text-slate-500 mb-1">Status Stok</label>
                <select name="status" id="status" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    <option value="">Semua Status</option>
                    <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>Stok Aman (&gt; Min)</option>
                    <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stok Kritis (&le; Min)</option>
                    <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                </select>
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="lg:col-span-2 flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                    Filter
                </button>
                <a href="{{ route('items.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors" title="Reset filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Items Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">SKU & Barang</th>
                        <th class="py-3 px-4 font-semibold">Kategori</th>
                        <th class="py-3 px-4 font-semibold">Gudang / Rak</th>
                        <th class="py-3 px-4 font-semibold text-right">Harga Beli</th>
                        <th class="py-3 px-4 font-semibold text-center">Stok Saat Ini</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-50/60 transition-colors {{ !$item->is_active ? 'opacity-60 bg-slate-50/30' : '' }}">
                        <!-- Item Name & SKU -->
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ substr($item->sku, 0, 3) }}
                                </div>
                                <div>
                                    <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                        {{ $item->name }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.2 rounded">{{ $item->sku }}</span>
                                        @if(!$item->is_active)
                                        <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider">[Nonaktif]</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $item->category->name ?? '-' }}
                            </span>
                        </td>

                        <!-- Warehouse & Location -->
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                            <p class="font-medium text-slate-800">{{ $item->warehouse->name ?? 'Semua Gudang' }}</p>
                            <p class="text-[10px] text-slate-400">Rak: {{ $item->location->code ?? 'Tanpa Rak' }}</p>
                        </td>

                        <!-- Purchase Price -->
                        <td class="py-3 px-4 text-right whitespace-nowrap font-medium text-slate-700">
                            Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                        </td>

                        <!-- Current Stock with Status Indicator -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex flex-col items-center">
                                <span class="text-sm font-extrabold {{ $item->current_stock <= 0 ? 'text-rose-600' : ($item->current_stock <= $item->min_stock ? 'text-amber-600' : 'text-slate-900') }}">
                                    {{ number_format($item->current_stock) }}
                                    <span class="text-[10px] font-normal text-slate-500">{{ $item->unit->symbol ?? '' }}</span>
                                </span>
                                <span class="text-[9px] text-slate-400">Min: {{ $item->min_stock }}</span>
                            </div>
                        </td>

                        <!-- Stock Status Badge -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($item->isOutOfStock())
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                Habis (0)
                            </span>
                            @elseif($item->isLowStock())
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                Kritis &le; Min
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Aman
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-1.5">
                                <a href="{{ route('items.show', $item) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Lihat Detail & Kartu Stok">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <a href="{{ route('items.edit', $item) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ubah Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                <form method="POST" action="{{ route('items.toggle-active', $item) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="{{ $item->is_active ? 'Nonaktifkan Barang' : 'Aktifkan Barang' }}">
                                        @if($item->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p class="text-sm font-semibold text-slate-700">Tidak ada data barang ditemukan</p>
                            <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau tambah barang baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $items->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
