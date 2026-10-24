@extends('layouts.app')

@section('title', 'Transaksi Barang Keluar')
@section('page_title', 'Barang Keluar')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Transaksi Barang Keluar (Outbound)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Catat pengeluaran barang ke divisi, pelanggan, atau cabang dengan jaminan validasi anti-stok minus</p>
        </div>
        <a href="{{ route('stock-outs.create') }}" id="btn-create-stock-out" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs shadow-rose-600/30 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
            Catat Pengeluaran Barang Keluar
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('stock-outs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <div class="lg:col-span-4">
                <label for="search" class="block text-[11px] font-semibold text-slate-500 mb-1">Cari No Dokumen / Penerima</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Contoh: OUT-202610..., Divisi Pemeliharaan..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <div class="lg:col-span-3">
                <label for="date_from" class="block text-[11px] font-semibold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <div class="lg:col-span-3">
                <label for="date_to" class="block text-[11px] font-semibold text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <div class="lg:col-span-2 flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                    Filter
                </button>
                <a href="{{ route('stock-outs.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors" title="Reset filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Outbound Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">No. Dokumen</th>
                        <th class="py-3 px-4 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Tujuan / Penerima</th>
                        <th class="py-3 px-4 font-semibold">Gudang Asal</th>
                        <th class="py-3 px-4 font-semibold text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stockOuts as $out)
                    <tr class="hover:bg-slate-50/60 transition-colors {{ $out->isCancelled() ? 'opacity-50 bg-slate-50/40 line-through-text' : '' }}">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                            <a href="{{ route('stock-outs.show', $out) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                {{ $out->document_number }}
                            </a>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                            {{ $out->date->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <p class="font-bold text-slate-800">{{ $out->recipient }}</p>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                            {{ $out->warehouse->name ?? 'Gudang Utama' }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="font-bold text-slate-800">{{ $out->total_items }}</span> jenis
                            <span class="text-rose-600 font-bold">({{ $out->total_quantity }} unit)</span>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($out->isCompleted())
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Selesai
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200" title="{{ $out->cancellation_reason }}">
                                Dibatalkan (Void)
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <a href="{{ route('stock-outs.show', $out) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 text-xs font-semibold transition-colors">
                                <span>Lihat Dokumen</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <p class="text-sm font-semibold text-slate-700">Belum ada transaksi barang keluar</p>
                            <p class="text-xs text-slate-500 mt-1">Klik tombol di atas untuk mencatat pengeluaran barang dari gudang.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stockOuts->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $stockOuts->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
