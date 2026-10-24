@extends('layouts.app')

@section('title', 'Laporan Mutasi Barang Periode')
@section('page_title', 'Laporan Mutasi')

@section('content')
<div class="space-y-6">

    <!-- Header Actions (hidden on print) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Laporan Mutasi Barang (FR-82)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Laporan rekapitulasi arus keluar-masuk barang pergudangan per periode tanggal</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor ke CSV
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Kuantitas Masuk (Periode Ini)</span>
                <p class="text-2xl font-black text-emerald-600 mt-1 font-mono">+{{ number_format($totalIn) }} <span class="text-xs font-semibold text-slate-500">Unit</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Kuantitas Keluar (Periode Ini)</span>
                <p class="text-2xl font-black text-rose-600 mt-1 font-mono">-{{ number_format($totalOut) }} <span class="text-xs font-semibold text-slate-500">Unit</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter Bar (hidden on print) -->
    <div class="no-print bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('reports.movements') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <div class="lg:col-span-3">
                <label for="date_from" class="block text-[11px] font-semibold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
            </div>

            <div class="lg:col-span-3">
                <label for="date_to" class="block text-[11px] font-semibold text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
            </div>

            <div class="lg:col-span-3">
                <label for="item_id" class="block text-[11px] font-semibold text-slate-500 mb-1">Barang Spesifik</label>
                <select name="item_id" id="item_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
                    <option value="">Semua Barang</option>
                    @foreach($items as $it)
                    <option value="{{ $it->id }}" {{ request('item_id') == $it->id ? 'selected' : '' }}>{{ $it->sku }} - {{ $it->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3 flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold">
                    Tampilkan
                </button>
                <a href="{{ route('reports.movements') }}" class="py-2 px-3 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Printable Report Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Rekapitulasi Mutasi Buku Besar</h3>
                <p class="text-xs text-slate-500">Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-2.5 px-3 font-semibold">Waktu</th>
                        <th class="py-2.5 px-3 font-semibold">No. Dokumen</th>
                        <th class="py-2.5 px-3 font-semibold">SKU & Nama Barang</th>
                        <th class="py-2.5 px-3 font-semibold text-center">Tipe</th>
                        <th class="py-2.5 px-3 font-semibold text-right">Saldo Awal</th>
                        <th class="py-2.5 px-3 font-semibold text-right">Jumlah</th>
                        <th class="py-2.5 px-3 font-semibold text-right">Saldo Akhir</th>
                        <th class="py-2.5 px-3 font-semibold">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movements as $m)
                    <tr class="hover:bg-slate-50/60 font-medium">
                        <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2.5 px-3 font-mono font-bold text-slate-800 whitespace-nowrap">{{ $m->reference_number ?? '-' }}</td>
                        <td class="py-2.5 px-3">
                            <span class="font-mono text-[10px] text-slate-400 block">{{ $m->item->sku ?? '-' }}</span>
                            <span class="text-slate-900 font-bold">{{ $m->item->name ?? '-' }}</span>
                        </td>
                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->quantity > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $m->type_label }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-right text-slate-500">{{ $m->balance_before }}</td>
                        <td class="py-2.5 px-3 text-right font-extrabold {{ $m->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $m->quantity > 0 ? '+'.$m->quantity : $m->quantity }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-black text-slate-900">{{ $m->balance_after }}</td>
                        <td class="py-2.5 px-3 text-slate-500 text-[11px] whitespace-nowrap">{{ $m->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-slate-400">Tidak ada data mutasi pada periode yang dipilih.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="no-print pt-4 border-t border-slate-100">
            {{ $movements->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
