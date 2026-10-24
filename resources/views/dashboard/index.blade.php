@extends('layouts.app')

@section('title', 'Dashboard Ringkasan Inventaris')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Welcome Banner with Dynamic Greeting -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 p-6 sm:p-8 text-white shadow-lg">
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                Ringkasan Stok Real-Time
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}! 👋
            </h2>
            <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                Anda masuk sebagai peran <strong class="text-white uppercase font-bold">{{ auth()->user()->role }}</strong>. Pantau pergerakan stok barang, deteksi peringatan stok kritis sebelum kehabisan, dan kelola arus logistik gudang secara akurat.
            </p>

            <div class="mt-5 flex flex-wrap gap-2.5">
                <a href="{{ route('stock-ins.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Catat Barang Masuk
                </a>
                <a href="{{ route('stock-outs.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    Catat Barang Keluar
                </a>
                <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Barang Baru
                </a>
            </div>
        </div>

        <!-- Ambient Glow -->
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Alert Banner If Critical Items Exist (FR-42, FR-43) -->
    @if($critical_total > 0)
    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200/80 shadow-xs flex items-start justify-between text-amber-900">
        <div class="flex items-start space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm mt-0.5">
                <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-950">Peringatan: Terdapat {{ $critical_total }} Barang Memerlukan Pengadaan Ulang!</h4>
                <p class="text-xs text-amber-800 mt-0.5">
                    {{ $out_of_stock_count }} barang stok habis (0 unit) dan {{ $low_stock_count }} barang berada pada atau di bawah batas minimum persediaan.
                </p>
            </div>
        </div>
        <a href="{{ route('items.index', ['status' => 'low']) }}" class="hidden sm:inline-flex items-center text-xs font-bold text-amber-900 hover:text-amber-950 underline underline-offset-2">
            Lihat Semua Barang Kritis &rarr;
        </a>
    </div>
    @endif

    <!-- Key Metrics 4-Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Card 1: Total Master Barang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Jenis Barang</p>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-3">{{ number_format($total_items) }}</p>
            <div class="mt-2 flex items-center text-xs text-slate-500">
                <span class="text-emerald-600 font-semibold flex items-center mr-1">Aktif</span> di katalog inventaris
            </div>
        </div>

        <!-- Card 2: Total Stok Unit Fisik -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Stok Tersedia</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-3">{{ number_format($total_stock) }} <span class="text-sm font-normal text-slate-500">Unit</span></p>
            <div class="mt-2 flex items-center text-xs text-slate-500">
                Akumulasi di seluruh gudang
            </div>
        </div>

        <!-- Card 3: Barang Kritis / Menipis -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Kritis / Habis</p>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600 mt-3">{{ $critical_total }} <span class="text-sm font-normal text-slate-500">Item</span></p>
            <div class="mt-2 flex items-center text-xs text-slate-500">
                <span class="text-rose-600 font-semibold mr-1">{{ $out_of_stock_count }} habis</span>, {{ $low_stock_count }} menipis
            </div>
        </div>

        <!-- Card 4: Transaksi Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Aktivitas Hari Ini</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-3">
                <div>
                    <span class="text-xl font-bold text-emerald-600">+{{ number_format($today_stock_in) }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Masuk</span>
                </div>
                <span class="text-slate-300">/</span>
                <div>
                    <span class="text-xl font-bold text-rose-600">-{{ number_format($today_stock_out) }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Keluar</span>
                </div>
            </div>
            <div class="mt-2 text-xs text-slate-500">
                Pergerakan fisik hari ini
            </div>
        </div>
    </div>

    <!-- 2 Columns: Critical Items Watchlist & Recent Movement Log -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column Left: Critical Stock Watchlist (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Perhatian Stok Menipis
                    </h3>
                    <p class="text-xs text-slate-500">Barang yang butuh restock segera</p>
                </div>
                <a href="{{ route('items.index', ['status' => 'low']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Lihat Semua &rarr;</a>
            </div>

            @if($critical_items->isEmpty())
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
                <svg class="w-12 h-12 text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-slate-700">Semua Stok Aman!</p>
                <p class="text-xs text-slate-500">Tidak ada barang yang berada di bawah stok minimum.</p>
            </div>
            @else
            <div class="space-y-3.5 flex-1">
                @foreach($critical_items as $cItem)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                    <div class="truncate mr-3">
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-[11px] font-semibold text-slate-500">{{ $cItem->sku }}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-700">{{ $cItem->category->name ?? '-' }}</span>
                        </div>
                        <p class="text-xs font-bold text-slate-800 truncate mt-0.5">{{ $cItem->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Min Stok: {{ $cItem->min_stock }} {{ $cItem->unit->symbol ?? '' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $cItem->current_stock <= 0 ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                            {{ $cItem->current_stock }} {{ $cItem->unit->symbol ?? '' }}
                        </span>
                        <a href="{{ route('stock-ins.create') }}" class="block text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 mt-1">
                            + Restock
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Column Right: Recent Activity / Movement Ledger (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                        Riwayat Mutasi Terkini
                    </h3>
                    <p class="text-xs text-slate-500">Log transaksi real-time terbaru</p>
                </div>
                <a href="{{ route('movements.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Buka Kartu Stok &rarr;</a>
            </div>

            @if($recent_transactions->isEmpty())
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
                <p class="text-sm">Belum ada transaksi mutasi yang tercatat.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[10px]">
                            <th class="pb-2.5 font-semibold">Waktu</th>
                            <th class="pb-2.5 font-semibold">Barang</th>
                            <th class="pb-2.5 font-semibold text-center">Tipe</th>
                            <th class="pb-2.5 font-semibold text-right">Jumlah</th>
                            <th class="pb-2.5 font-semibold text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($recent_transactions as $tx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-2.5 text-slate-500 whitespace-nowrap">
                                {{ $tx->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-2.5">
                                <a href="{{ route('items.show', $tx->item_id) }}" class="font-bold text-slate-800 hover:text-indigo-600 truncate max-w-[180px] block">
                                    {{ $tx->item->name ?? '-' }}
                                </a>
                                <span class="font-mono text-[10px] text-slate-400">{{ $tx->reference_number ?? '-' }}</span>
                            </td>
                            <td class="py-2.5 text-center whitespace-nowrap">
                                @if($tx->type === 'in')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Masuk</span>
                                @elseif($tx->type === 'out')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Keluar</span>
                                @elseif($tx->type === 'adjustment')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Penyesuaian</span>
                                @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $tx->type_label }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 text-right font-bold whitespace-nowrap {{ $tx->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->quantity > 0 ? '+'.$tx->quantity : $tx->quantity }} {{ $tx->item->unit->symbol ?? '' }}
                            </td>
                            <td class="py-2.5 text-right font-bold text-slate-700 whitespace-nowrap">
                                {{ $tx->balance_after }}
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
