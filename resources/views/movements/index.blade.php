@extends('layouts.app')

@section('title', 'Riwayat Mutasi & Kartu Stok Global')
@section('page_title', 'Kartu Stok & Mutasi')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Riwayat Mutasi Stok Global</h2>
            <p class="text-xs text-slate-500 mt-0.5">Audit log pergerakan stok, saldo berjalan, dan referensi transaksi (FR-41)</p>
        </div>
        <a href="{{ route('reports.movements') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Laporan Mutasi Periode & Ekspor
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('movements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-3">
                <label for="search" class="block text-[11px] font-semibold text-slate-500 mb-1">Cari Dokumen / Catatan</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="No. dokumen, SKU, nama..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <!-- Item Filter -->
            <div class="lg:col-span-3">
                <label for="item_id" class="block text-[11px] font-semibold text-slate-500 mb-1">Pilih Spesifik Barang</label>
                <select name="item_id" id="item_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    <option value="">Semua Barang</option>
                    @foreach($items as $it)
                    <option value="{{ $it->id }}" {{ request('item_id') == $it->id ? 'selected' : '' }}>{{ $it->sku }} - {{ $it->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div class="lg:col-span-2">
                <label for="type" class="block text-[11px] font-semibold text-slate-500 mb-1">Tipe Mutasi</label>
                <select name="type" id="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    <option value="">Semua Tipe</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Barang Masuk</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Barang Keluar</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian (Opname)</option>
                    <option value="void_in" {{ request('type') === 'void_in' ? 'selected' : '' }}>Batal Masuk</option>
                    <option value="void_out" {{ request('type') === 'void_out' ? 'selected' : '' }}>Batal Keluar</option>
                </select>
            </div>

            <!-- Date Range -->
            <div class="lg:col-span-2">
                <label for="date_from" class="block text-[11px] font-semibold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <!-- Action Buttons -->
            <div class="lg:col-span-2 flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                    Filter
                </button>
                <a href="{{ route('movements.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Movement Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">Waktu</th>
                        <th class="py-3 px-4 font-semibold">Barang</th>
                        <th class="py-3 px-4 font-semibold text-center">Tipe Mutasi</th>
                        <th class="py-3 px-4 font-semibold">No. Dokumen</th>
                        <th class="py-3 px-4 font-semibold text-right">Saldo Awal</th>
                        <th class="py-3 px-4 font-semibold text-right">Perubahan</th>
                        <th class="py-3 px-4 font-semibold text-right">Saldo Akhir</th>
                        <th class="py-3 px-4 font-semibold">Keterangan</th>
                        <th class="py-3 px-4 font-semibold">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($movements as $m)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                            {{ $m->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('movements.item', $m->item_id) }}" class="font-bold text-slate-900 hover:text-indigo-600 block truncate max-w-[200px]" title="Buka Kartu Stok Barang">
                                {{ $m->item->name ?? '-' }}
                            </a>
                            <span class="font-mono text-[10px] text-slate-400">{{ $m->item->sku ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($m->type === 'in')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Masuk</span>
                            @elseif($m->type === 'out')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Keluar</span>
                            @elseif($m->type === 'adjustment')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Opname</span>
                            @elseif($m->type === 'void_in')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Batal Masuk</span>
                            @elseif($m->type === 'void_out')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">Batal Keluar</span>
                            @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800">{{ $m->type_label }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-mono whitespace-nowrap text-slate-700">
                            {{ $m->reference_number ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap text-slate-500">
                            {{ $m->balance_before }}
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap font-extrabold {{ $m->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $m->quantity > 0 ? '+'.$m->quantity : $m->quantity }} {{ $m->item->unit->symbol ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap font-extrabold text-slate-900">
                            {{ $m->balance_after }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 max-w-[220px] truncate" title="{{ $m->notes }}">
                            {{ $m->notes ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-slate-500 whitespace-nowrap text-[11px]">
                            {{ $m->user->name ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <p class="text-sm font-semibold text-slate-700">Belum ada catatan mutasi</p>
                            <p class="text-xs text-slate-500 mt-1">Mutasi otomatis tercipta saat terjadi transaksi barang masuk atau keluar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $movements->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
