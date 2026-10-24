@extends('layouts.app')

@section('title', 'Sesi Stock Opname Fisik')
@section('page_title', 'Stock Opname')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Stock Opname Fisik Gudang (FR-50 - FR-52)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Penghitungan fisik berkala, deteksi selisih stok sistem, dan persetujuan penyesuaian oleh Manajer</p>
        </div>
        <a href="{{ route('stock-opnames.create') }}" id="btn-create-opname" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Sesi Opname Baru
        </a>
    </div>

    <!-- Opnames Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">No. Dokumen Opname</th>
                        <th class="py-3 px-4 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Gudang</th>
                        <th class="py-3 px-4 font-semibold">Petugas Pemeriksa</th>
                        <th class="py-3 px-4 font-semibold">Disetujui Oleh</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($opnames as $op)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3 px-4 font-mono font-bold text-indigo-600 whitespace-nowrap">
                            <a href="{{ route('stock-opnames.show', $op) }}" class="hover:underline">
                                {{ $op->document_number }}
                            </a>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">{{ $op->date->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-800">{{ $op->warehouse->name ?? 'Semua Gudang' }}</td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-700">{{ $op->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600">{{ $op->approvedByUser->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($op->isApproved())
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Disetujui
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                Menunggu Persetujuan
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <a href="{{ route('stock-opnames.show', $op) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 text-xs font-semibold">
                                <span>Tinjau Sesi</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">Belum ada sesi stock opname.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($opnames->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $opnames->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
