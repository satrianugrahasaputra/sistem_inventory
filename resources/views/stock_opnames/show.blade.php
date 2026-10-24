@extends('layouts.app')

@section('title', 'Tinjau Stock Opname: ' . $stockOpname->document_number)
@section('page_title', 'Tinjau Sesi Opname')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Navigation & Action -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('stock-opnames.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Sesi Opname
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Hasil Opname
            </button>

            @if($stockOpname->isDraft() && auth()->user()->hasRole(['admin', 'manager']))
            <form method="POST" action="{{ route('stock-opnames.approve', $stockOpname) }}" onsubmit="return confirm('Apakah Anda yakin menyetujui sesi opname ini? Stok sistem akan disesuaikan otomatis.')">
                @csrf
                <button type="submit" id="btn-approve-opname" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui Opname & Koreksi Stok
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    @if($stockOpname->isDraft())
    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Sesi opname ini masih berstatus <strong>DRAFT</strong>. Memerlukan persetujuan dari Manajer atau Admin untuk memperbarui saldo stok fisik.</span>
        </div>
    </div>
    @else
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2.5">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Sesi opname telah <strong>DISETUJUI</strong> oleh {{ $stockOpname->approvedByUser->name ?? 'Manajer' }} pada {{ $stockOpname->approved_at ? $stockOpname->approved_at->format('d/m/Y H:i') : '-' }}. Saldo fisik telah disesuaikan di kartu stok.</span>
    </div>
    @endif

    <!-- Main Opname Report Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-black text-slate-900">BERITA ACARA STOCK OPNAME</h2>
                <p class="text-xs text-slate-500 mt-0.5 font-mono">No. Dokumen: {{ $stockOpname->document_number }}</p>
            </div>
            <div class="sm:text-right text-xs">
                <span class="text-slate-500">Tanggal:</span>
                <p class="font-bold text-slate-900">{{ $stockOpname->date->format('d F Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs p-4 rounded-xl bg-slate-50 border border-slate-200/60">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Gudang / Lokasi:</span>
                <strong class="text-slate-900">{{ $stockOpname->warehouse->name ?? 'Semua Gudang' }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Petugas Penghitung:</span>
                <strong class="text-slate-900">{{ $stockOpname->user->name ?? '-' }}</strong>
            </div>
        </div>

        <!-- Details Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-2.5 px-3 font-semibold text-center w-12">No.</th>
                        <th class="py-2.5 px-3 font-semibold">SKU & Nama Barang</th>
                        <th class="py-2.5 px-3 font-semibold text-center w-28">Stok Sistem</th>
                        <th class="py-2.5 px-3 font-semibold text-center w-28">Stok Fisik</th>
                        <th class="py-2.5 px-3 font-semibold text-center w-28">Selisih</th>
                        <th class="py-2.5 px-3 font-semibold">Catatan Temuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($stockOpname->details as $idx => $d)
                    <tr class="hover:bg-slate-50">
                        <td class="py-2.5 px-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                        <td class="py-2.5 px-3">
                            <span class="font-mono text-[10px] text-slate-400 block">{{ $d->item->sku ?? '-' }}</span>
                            <span class="font-bold text-slate-900">{{ $d->item->name ?? '-' }}</span>
                        </td>
                        <td class="py-2.5 px-3 text-center font-bold text-slate-600">{{ $d->system_stock }} {{ $d->item->unit->symbol ?? '' }}</td>
                        <td class="py-2.5 px-3 text-center font-extrabold text-slate-900">{{ $d->physical_stock }} {{ $d->item->unit->symbol ?? '' }}</td>
                        <td class="py-2.5 px-3 text-center font-black {{ $d->difference === 0 ? 'text-slate-400' : ($d->difference > 0 ? 'text-emerald-600' : 'text-rose-600') }}">
                            {{ $d->difference > 0 ? '+'.$d->difference : $d->difference }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-500">{{ $d->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
