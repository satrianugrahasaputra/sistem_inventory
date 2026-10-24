@extends('layouts.app')

@section('title', 'Kartu Stok Barang: ' . $item->name)
@section('page_title', 'Kartu Stok')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar (hidden on print) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('items.show', $item) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Profil Barang
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Kartu Stok Fisik
            </button>
            <a href="{{ route('stock-ins.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-all">
                + Tambah Stok Masuk
            </a>
        </div>
    </div>

    <!-- Official Kartu Stok Layout -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">KARTU STOK PERSEDIAAN BARANG</h2>
                <p class="text-xs text-slate-500 mt-0.5">Buku Besar Mutasi Fisik & Saldo Berjalan Gudang</p>
            </div>
            <div class="sm:text-right">
                <p class="text-xs text-slate-500">Stok Akhir Saat Ini:</p>
                <div class="text-2xl font-black {{ $item->current_stock <= $item->min_stock ? 'text-amber-600' : 'text-slate-900' }}">
                    {{ number_format($item->current_stock) }} <span class="text-sm font-semibold text-slate-500">{{ $item->unit->symbol ?? '' }}</span>
                </div>
            </div>
        </div>

        <!-- Item Identity Header Info Box -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kode SKU:</span>
                <strong class="font-mono text-slate-900 font-bold text-sm">{{ $item->sku }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Nama Barang:</span>
                <strong class="text-slate-900 font-bold">{{ $item->name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kategori / Satuan:</span>
                <strong class="text-slate-900 font-bold">{{ $item->category->name ?? '-' }} / {{ $item->unit->symbol ?? '-' }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Stok Minimum:</span>
                <strong class="text-slate-900 font-bold">{{ $item->min_stock }} {{ $item->unit->symbol ?? '' }}</strong>
            </div>
        </div>

        <!-- Ledger Movements Table with Running Balance -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-2.5 px-3 font-semibold text-center w-12">No.</th>
                        <th class="py-2.5 px-3 font-semibold w-32">Tanggal & Waktu</th>
                        <th class="py-2.5 px-3 font-semibold w-36">No. Bukti / Referensi</th>
                        <th class="py-2.5 px-3 font-semibold">Keterangan Mutasi</th>
                        <th class="py-2.5 px-3 font-semibold text-center w-24 bg-emerald-50 text-emerald-900 border-x border-emerald-100">Masuk (+)</th>
                        <th class="py-2.5 px-3 font-semibold text-center w-24 bg-rose-50 text-rose-900 border-r border-rose-100">Keluar (-)</th>
                        <th class="py-2.5 px-3 font-semibold text-center w-28 bg-slate-200/60 text-slate-900">Saldo Akhir</th>
                        <th class="py-2.5 px-3 font-semibold w-28">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($movements as $idx => $m)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-2.5 px-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                        <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2.5 px-3 font-mono font-bold text-slate-800 whitespace-nowrap">{{ $m->reference_number ?? '-' }}</td>
                        <td class="py-2.5 px-3 text-slate-700">{{ $m->notes ?? $m->type_label }}</td>

                        <!-- In Column -->
                        <td class="py-2.5 px-3 text-center font-bold text-emerald-700 bg-emerald-50/30 border-x border-emerald-100">
                            {{ $m->quantity > 0 ? '+'.$m->quantity : '-' }}
                        </td>

                        <!-- Out Column -->
                        <td class="py-2.5 px-3 text-center font-bold text-rose-700 bg-rose-50/30 border-r border-rose-100">
                            {{ $m->quantity < 0 ? abs($m->quantity) : '-' }}
                        </td>

                        <!-- Running Balance Column -->
                        <td class="py-2.5 px-3 text-center font-black text-slate-900 bg-slate-100/50 text-sm">
                            {{ $m->balance_after }}
                        </td>

                        <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap text-[11px]">{{ $m->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-slate-400">
                            Belum ada riwayat mutasi untuk barang ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="no-print pt-4">
            {{ $movements->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
