@extends('layouts.app')

@section('title', 'Laporan Posisi Stok Gudang')
@section('page_title', 'Laporan Stok')

@section('content')
<div class="space-y-6">

    <!-- Header Actions (hidden on print) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Laporan Posisi Stok Gudang (FR-81)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan kuantiti fisik, stok minimum, dan valuasi aset persediaan barang</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor ke CSV / Excel
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Valuation Summary Banner -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-indigo-900 to-slate-900 text-white shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs uppercase font-semibold text-indigo-300 tracking-wider">Total Nilai Estimasi Aset Persediaan</span>
            <h3 class="text-3xl font-black mt-1 font-mono">
                Rp {{ number_format($totalValuation, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-slate-300 mt-1">Dihitung dari akumulasi: [Stok Fisik &times; Harga Beli Standar]</p>
        </div>
        <div class="text-xs sm:text-right">
            <span class="text-slate-400">Dicetak per:</span>
            <p class="font-bold text-white text-sm">{{ now()->translatedFormat('d F Y, H:i') }}</p>
        </div>
    </div>

    <!-- Filter Bar (hidden on print) -->
    <div class="no-print bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('reports.stock') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-12 gap-3">
            <div class="lg:col-span-4">
                <label for="category_id" class="block text-[11px] font-semibold text-slate-500 mb-1">Kategori</label>
                <select name="category_id" id="category_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="warehouse_id" class="block text-[11px] font-semibold text-slate-500 mb-1">Gudang</label>
                <select name="warehouse_id" id="warehouse_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
                    <option value="">Semua Gudang</option>
                    @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3">
                <label for="status" class="block text-[11px] font-semibold text-slate-500 mb-1">Status Ketersediaan</label>
                <select name="status" id="status" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
                    <option value="">Semua Status</option>
                    <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>Stok Aman (&gt; Min)</option>
                    <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stok Kritis (&le; Min)</option>
                    <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                </select>
            </div>

            <div class="lg:col-span-2 flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold">
                    Terapkan
                </button>
                <a href="{{ route('reports.stock') }}" class="py-2 px-3 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Printable Report Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-3 font-semibold text-center w-10">No.</th>
                        <th class="py-3 px-3 font-semibold">SKU & Nama Barang</th>
                        <th class="py-3 px-3 font-semibold">Kategori</th>
                        <th class="py-3 px-3 font-semibold">Gudang / Rak</th>
                        <th class="py-3 px-3 font-semibold text-center">Stok Fisik</th>
                        <th class="py-3 px-3 font-semibold text-center">Batas Min</th>
                        <th class="py-3 px-3 font-semibold text-right">Harga Beli</th>
                        <th class="py-3 px-3 font-semibold text-right">Total Nilai</th>
                        <th class="py-3 px-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $i => $item)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-2.5 px-3 text-center text-slate-400 font-mono">{{ $items->firstItem() + $i }}</td>
                        <td class="py-2.5 px-3">
                            <span class="font-mono text-[10px] text-slate-400 block">{{ $item->sku }}</span>
                            <strong class="font-bold text-slate-900">{{ $item->name }}</strong>
                        </td>
                        <td class="py-2.5 px-3 whitespace-nowrap">{{ $item->category->name ?? '-' }}</td>
                        <td class="py-2.5 px-3 whitespace-nowrap text-slate-600">
                            {{ $item->warehouse->name ?? '-' }} / {{ $item->location->code ?? '-' }}
                        </td>
                        <td class="py-2.5 px-3 text-center font-extrabold {{ $item->current_stock <= 0 ? 'text-rose-600' : ($item->current_stock <= $item->min_stock ? 'text-amber-600' : 'text-slate-900') }}">
                            {{ number_format($item->current_stock) }} {{ $item->unit->symbol ?? '' }}
                        </td>
                        <td class="py-2.5 px-3 text-center text-slate-500">
                            {{ $item->min_stock }} {{ $item->unit->symbol ?? '' }}
                        </td>
                        <td class="py-2.5 px-3 text-right text-slate-700 whitespace-nowrap">
                            Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-bold text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($item->current_stock * $item->purchase_price, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                            @if($item->isOutOfStock())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Habis</span>
                            @elseif($item->isLowStock())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Kritis</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aman</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-10 text-center text-slate-400">Tidak ada data stok yang sesuai filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="no-print pt-4 border-t border-slate-100">
            {{ $items->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
