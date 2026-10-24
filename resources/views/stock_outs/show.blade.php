@extends('layouts.app')

@section('title', 'Dokumen Pengeluaran: ' . $stockOut->document_number)
@section('page_title', 'Bukti Pengeluaran Barang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ voidModalOpen: false }">

    <!-- Top Action Bar (hidden on print) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('stock-outs.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Transaksi Keluar
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Bukti Pengeluaran
            </button>

            @if($stockOut->isCompleted())
            <button type="button" @click="voidModalOpen = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Batalkan Transaksi (Void)
            </button>
            @endif
        </div>
    </div>

    <!-- Void Notice If Cancelled -->
    @if($stockOut->isCancelled())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start gap-3">
        <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="font-bold text-rose-950">Transaksi ini telah dibatalkan (VOID)!</p>
            <p class="mt-0.5">Dibatalkan oleh <strong class="text-rose-950">{{ $stockOut->cancelledByUser->name ?? 'User' }}</strong> pada {{ $stockOut->cancelled_at ? $stockOut->cancelled_at->format('d/m/Y H:i') : '-' }}.</p>
            <p class="mt-1 italic bg-rose-100/60 p-2 rounded text-rose-800">Alasan pembatalan: "{{ $stockOut->cancellation_reason }}"</p>
        </div>
    </div>
    @endif

    <!-- Formal Printable Document Receipt -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-10 space-y-8">
        <!-- Document Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-lg font-black tracking-tight text-slate-900">GudangPro</span>
                    <span class="text-xs px-2 py-0.5 rounded bg-rose-100 font-semibold text-rose-800">OUTBOUND</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Surat Perintah / Bukti Pengeluaran Barang</p>
            </div>

            <div class="sm:text-right">
                <span class="font-mono text-base font-extrabold text-rose-700 block">
                    {{ $stockOut->document_number }}
                </span>
                <span class="text-xs text-slate-500">
                    Tanggal: {{ $stockOut->date->format('d F Y') }}
                </span>
            </div>
        </div>

        <!-- Metadata Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div class="space-y-1.5 p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tujuan / Penerima</p>
                <p class="text-sm font-bold text-slate-900">{{ $stockOut->recipient }}</p>
                <p class="text-slate-500">Catatan: {{ $stockOut->notes ?? '-' }}</p>
            </div>

            <div class="space-y-1.5 p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Asal Gudang & Petugas</p>
                <p class="text-sm font-bold text-slate-900">{{ $stockOut->warehouse->name ?? 'Gudang Utama' }}</p>
                <p class="text-slate-600">Dikeluarkan Oleh: <strong class="text-slate-800">{{ $stockOut->user->name ?? '-' }}</strong></p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-2.5 px-3 font-semibold text-center w-12">No.</th>
                        <th class="py-2.5 px-3 font-semibold">SKU & Nama Barang</th>
                        <th class="py-2.5 px-3 font-semibold text-center">Jumlah Keluar</th>
                        <th class="py-2.5 px-3 font-semibold">Keterangan Khusus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stockOut->details as $index => $detail)
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3 px-3 text-center text-slate-400 font-mono">{{ $index + 1 }}</td>
                        <td class="py-3 px-3">
                            <span class="font-mono text-[10px] text-indigo-600 bg-indigo-50 px-1.5 py-0.2 rounded font-semibold">{{ $detail->item->sku ?? '-' }}</span>
                            <p class="font-bold text-slate-900 mt-0.5">{{ $detail->item->name ?? '-' }}</p>
                        </td>
                        <td class="py-3 px-3 text-center font-bold text-rose-600 text-sm">
                            {{ $detail->quantity }} {{ $detail->item->unit->symbol ?? '' }}
                        </td>
                        <td class="py-3 px-3 text-slate-500">
                            {{ $detail->notes ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50/80 font-bold border-t border-slate-200">
                        <td colspan="2" class="py-3 px-3 text-slate-600">Total Kuantitas Barang Keluar</td>
                        <td class="py-3 px-3 text-center text-rose-600 font-extrabold text-sm">{{ $stockOut->total_quantity }} Unit</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signature Lines for Print -->
        <div class="pt-10 grid grid-cols-2 text-center text-xs text-slate-600">
            <div>
                <p>Penerima Barang / Pemohon</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-800">( {{ $stockOut->recipient }} )</p>
            </div>
            <div>
                <p>Petugas Gudang Penyerah</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-800">( {{ $stockOut->user->name ?? 'Petugas Gudang' }} )</p>
            </div>
        </div>
    </div>

    <!-- Modal Pembatalan (Void) -->
    <div x-show="voidModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.away="voidModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="text-center">
                <h3 class="text-base font-bold text-slate-900">Batalkan Transaksi Keluar?</h3>
                <p class="text-xs text-slate-500 mt-1">Stok yang telah dikeluarkan akan dikembalikan secara otomatis ke dalam gudang.</p>
            </div>

            <form method="POST" action="{{ route('stock-outs.void', $stockOut) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="reason" class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pembatalan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" id="reason" rows="3" required placeholder="Contoh: Barang batal diambil oleh divisi pemohon..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="voidModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs">
                        Konfirmasi Pembatalan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
