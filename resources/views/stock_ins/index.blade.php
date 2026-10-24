@extends('layouts.app')

@section('title', 'Transaksi Barang Masuk')
@section('page_title', 'Barang Masuk')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Transaksi Barang Masuk (Inbound)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Catat penerimaan stok dari supplier, surat jalan, dan penambahan saldo otomatis</p>
        </div>
        <a href="{{ route('stock-ins.create') }}" id="btn-create-stock-in" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs shadow-emerald-600/30 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Penerimaan Barang Masuk
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('stock-ins.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <div class="lg:col-span-4">
                <label for="search" class="block text-[11px] font-semibold text-slate-500 mb-1">Cari No Dokumen / Supplier</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Contoh: IN-202610..., PT Maju..."
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
                <a href="{{ route('stock-ins.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors" title="Reset filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Inbound Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">No. Dokumen</th>
                        <th class="py-3 px-4 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Supplier / Mitra</th>
                        <th class="py-3 px-4 font-semibold">Gudang</th>
                        <th class="py-3 px-4 font-semibold text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 font-semibold text-right">Total Nilai</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stockIns as $in)
                    <tr class="hover:bg-slate-50/60 transition-colors {{ $in->isCancelled() ? 'opacity-50 bg-slate-50/40 line-through-text' : '' }}">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                            <a href="{{ route('stock-ins.show', $in) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                {{ $in->document_number }}
                            </a>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                            {{ $in->date->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <p class="font-bold text-slate-800">{{ $in->supplier->name ?? '-' }}</p>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                            {{ $in->warehouse->name ?? 'Gudang Utama' }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="font-bold text-slate-800">{{ $in->total_items }}</span> jenis
                            <span class="text-slate-400">({{ $in->total_quantity }} unit)</span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap font-bold text-slate-800">
                            Rp {{ number_format($in->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($in->isCompleted())
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Selesai
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200" title="{{ $in->cancellation_reason }}">
                                Dibatalkan (Void)
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <a href="{{ route('stock-ins.show', $in) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 text-xs font-semibold transition-colors">
                                <span>Lihat Dokumen</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <p class="text-sm font-semibold text-slate-700">Belum ada transaksi barang masuk</p>
                            <p class="text-xs text-slate-500 mt-1">Klik tombol di atas untuk mencatat penerimaan barang pertama.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stockIns->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $stockIns->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
