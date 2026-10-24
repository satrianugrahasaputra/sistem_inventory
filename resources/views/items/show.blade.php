@extends('layouts.app')

@section('title', 'Detail Barang: ' . $item->name)
@section('page_title', 'Detail Barang')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('items.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Barang
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('movements.item', $item) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Kartu Stok Lengkap
            </a>
            <a href="{{ route('items.edit', $item) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs shadow-indigo-600/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Ubah Barang
            </a>
        </div>
    </div>

    <!-- Overview 2-Columns Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1: Item Profile Card (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
            <div class="text-center pb-5 border-b border-slate-100">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white text-2xl font-bold shadow-md shadow-indigo-500/20 mb-3">
                    {{ substr($item->sku, 0, 3) }}
                </div>
                <h3 class="text-lg font-bold text-slate-900 leading-snug">{{ $item->name }}</h3>
                <span class="inline-block font-mono text-xs text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full font-semibold mt-1">
                    {{ $item->sku }}
                </span>
            </div>

            <!-- Stock Status Big Indicator -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Stok Fisik Tersedia</p>
                <div class="flex items-center justify-center gap-1.5 mt-1">
                    <span class="text-3xl font-extrabold {{ $item->current_stock <= 0 ? 'text-rose-600' : ($item->current_stock <= $item->min_stock ? 'text-amber-600' : 'text-slate-900') }}">
                        {{ number_format($item->current_stock) }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">{{ $item->unit->symbol ?? '' }}</span>
                </div>
                <div class="mt-2 flex items-center justify-center gap-2">
                    @if($item->isOutOfStock())
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                        Stok Habis
                    </span>
                    @elseif($item->isLowStock())
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        Kritis &le; Min ({{ $item->min_stock }})
                    </span>
                    @else
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Aman (Min: {{ $item->min_stock }})
                    </span>
                    @endif
                </div>
            </div>

            <!-- Details List -->
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400 font-medium">Kategori:</span>
                    <span class="font-bold text-slate-800">{{ $item->category->name ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400 font-medium">Satuan Ukur:</span>
                    <span class="font-bold text-slate-800">{{ $item->unit->name ?? '-' }} ({{ $item->unit->symbol ?? '-' }})</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400 font-medium">Gudang Default:</span>
                    <span class="font-bold text-slate-800">{{ $item->warehouse->name ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400 font-medium">Lokasi / Rak:</span>
                    <span class="font-bold text-slate-800">{{ $item->location->code ?? 'Tanpa Rak' }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400 font-medium">Harga Beli:</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400 font-medium">Harga Jual:</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400 font-medium">Valuasi Stok:</span>
                    <span class="font-bold text-emerald-700">Rp {{ number_format($item->current_stock * $item->purchase_price, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($item->description)
            <div class="pt-3 border-t border-slate-100 text-xs">
                <p class="font-semibold text-slate-500 uppercase tracking-wider text-[10px] mb-1">Spesifikasi / Keterangan:</p>
                <p class="text-slate-700 leading-relaxed">{{ $item->description }}</p>
            </div>
            @endif

            <!-- Quick Restock Shortcut -->
            <div class="pt-2">
                <a href="{{ route('stock-ins.create') }}" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs flex items-center justify-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Catat Penerimaan Barang Masuk
                </a>
            </div>
        </div>

        <!-- Column 2: Kartu Riwayat Mutasi (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Kartu Stok (Mutasi Terakhir)</h3>
                    <p class="text-xs text-slate-500">Rekam jejak setiap perubahan saldo fisik barang ini</p>
                </div>
                <a href="{{ route('movements.item', $item) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Lihat Seluruh Mutasi &rarr;
                </a>
            </div>

            @if($movements->isEmpty())
            <div class="py-12 text-center text-slate-400">
                <p class="text-sm font-semibold text-slate-700">Belum Ada Riwayat Mutasi</p>
                <p class="text-xs text-slate-500 mt-1">Lakukan transaksi barang masuk atau keluar untuk melihat pergerakan kartu stok.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <th class="py-2.5 px-3 font-semibold">Waktu</th>
                            <th class="py-2.5 px-3 font-semibold">Tipe</th>
                            <th class="py-2.5 px-3 font-semibold">Referensi</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Saldo Awal</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Jumlah</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Saldo Akhir</th>
                            <th class="py-2.5 px-3 font-semibold">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($movements as $m)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap">
                                {{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                @if($m->type === 'in')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Masuk</span>
                                @elseif($m->type === 'out')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Keluar</span>
                                @elseif($m->type === 'adjustment')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Penyesuaian</span>
                                @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $m->type_label }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="font-mono text-slate-700">{{ $m->reference_number ?? '-' }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-right text-slate-500 whitespace-nowrap">
                                {{ $m->balance_before }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold whitespace-nowrap {{ $m->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $m->quantity > 0 ? '+'.$m->quantity : $m->quantity }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                {{ $m->balance_after }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap text-[11px]">
                                {{ $m->user->name ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>

</div>
@endsection
