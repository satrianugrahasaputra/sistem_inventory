<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Inventory Gudang') | SmartWarehouse</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN Fallback & Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col antialiased text-slate-800" x-data="{ mobileMenuOpen: false }">

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="no-print hidden md:flex md:flex-col md:w-64 bg-slate-900 border-r border-slate-800 flex-shrink-0 z-30 transition-all duration-300">
            <!-- App Logo & Brand -->
            <div class="h-16 flex items-center px-6 border-b border-slate-800/80 bg-slate-950/40">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-violet-400 flex items-center justify-center text-white shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-base font-bold text-white tracking-tight flex items-center gap-1.5">
                            GudangPro
                            <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">v1.0</span>
                        </span>
                        <p class="text-[11px] text-slate-400">Inventory System</p>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                <!-- Group 1: Utama -->
                <div>
                    <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Utama</p>
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" id="nav-dashboard" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                    </nav>
                </div>

                <!-- Group 2: Operasional Transaksi -->
                <div>
                    <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Transaksi Stok</p>
                    <nav class="space-y-1">
                        <a href="{{ route('stock-ins.index') }}" id="nav-stock-in" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('stock-ins.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('stock-ins.*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Barang Masuk
                            </div>
                            <span class="text-xs bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded border border-emerald-500/30">In</span>
                        </a>

                        <a href="{{ route('stock-outs.index') }}" id="nav-stock-out" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('stock-outs.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('stock-outs.*') ? 'text-white' : 'text-rose-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Barang Keluar
                            </div>
                            <span class="text-xs bg-rose-500/20 text-rose-300 px-1.5 py-0.5 rounded border border-rose-500/30">Out</span>
                        </a>

                        <a href="{{ route('movements.index') }}" id="nav-movements" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('movements.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('movements.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Kartu Stok / Mutasi
                        </a>

                        <a href="{{ route('stock-opnames.index') }}" id="nav-opname" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('stock-opnames.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('stock-opnames.*') ? 'text-white' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Stock Opname
                        </a>
                    </nav>
                </div>

                <!-- Group 3: Data Master -->
                <div>
                    <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Master Data</p>
                    <nav class="space-y-1">
                        <a href="{{ route('items.index') }}" id="nav-items" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('items.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('items.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Data Barang
                        </a>

                        <a href="{{ route('categories.index') }}" id="nav-categories" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Kategori Barang
                        </a>

                        <a href="{{ route('units.index') }}" id="nav-units" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('units.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('units.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Satuan
                        </a>

                        <a href="{{ route('suppliers.index') }}" id="nav-suppliers" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('suppliers.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Supplier / Mitra
                        </a>

                        <a href="{{ route('warehouses.index') }}" id="nav-warehouses" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('warehouses.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('warehouses.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Gudang & Rak
                        </a>
                    </nav>
                </div>

                <!-- Group 4: Laporan -->
                <div>
                    <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Laporan</p>
                    <nav class="space-y-1">
                        <a href="{{ route('reports.stock') }}" id="nav-report-stock" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.stock') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.stock') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Laporan Stok Saat Ini
                        </a>
                        <a href="{{ route('reports.movements') }}" id="nav-report-movements" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.movements') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.movements') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Laporan Mutasi Periode
                        </a>
                    </nav>
                </div>

                <!-- Group 5: Administrator -->
                @if(auth()->user()->isAdmin())
                <div>
                    <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Administrasi</p>
                    <nav class="space-y-1">
                        <a href="{{ route('users.index') }}" id="nav-users" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Kelola Pengguna
                        </a>
                        <a href="{{ route('audit-logs.index') }}" id="nav-audit-logs" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('audit-logs.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('audit-logs.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Audit Log Aktivitas
                        </a>
                    </nav>
                </div>
                @endif
            </div>

            <!-- User Info at Bottom of Sidebar -->
            <div class="p-3 border-t border-slate-800 bg-slate-950/60">
                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/80 border border-slate-800">
                    <div class="flex items-center space-x-3 truncate">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-medium 
                                {{ auth()->user()->role === 'admin' ? 'bg-purple-900/60 text-purple-300 border border-purple-700/50' : (auth()->user()->role === 'manager' ? 'bg-blue-900/60 text-blue-300 border border-blue-700/50' : 'bg-emerald-900/60 text-emerald-300 border border-emerald-700/50') }}">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
            <!-- Top Navigation Bar -->
            <header class="no-print h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6 z-20 shadow-xs">
                <div class="flex items-center space-x-3">
                    <!-- Mobile Hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumbs / Page Title -->
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="text-slate-400 font-normal">Sistem Inventory</span>
                        <span class="text-slate-300">/</span>
                        <span class="font-semibold text-slate-800">@yield('page_title', 'Halaman Utama')</span>
                    </div>
                </div>

                <!-- Quick Action Buttons & Real-Time Date Indicator -->
                <div class="flex items-center space-x-3">
                    <div class="hidden lg:flex items-center text-xs text-slate-500 bg-slate-100/80 px-3 py-1.5 rounded-full border border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>
                        <span class="font-medium text-slate-600">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <!-- Quick Transaction Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" id="btn-quick-action" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs shadow-indigo-600/30 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Transaksi Baru</span>
                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-50 text-xs">
                            <a href="{{ route('stock-ins.create') }}" class="flex items-center px-3 py-2 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2.5"></span>
                                Catat Barang Masuk
                            </a>
                            <a href="{{ route('stock-outs.create') }}" class="flex items-center px-3 py-2 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium">
                                <span class="w-2 h-2 rounded-full bg-rose-500 mr-2.5"></span>
                                Catat Barang Keluar
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <a href="{{ route('items.create') }}" class="flex items-center px-3 py-2 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2.5"></span>
                                Tambah Barang Baru
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <!-- Flash Notification Messages -->
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200/80 shadow-xs flex items-center justify-between text-emerald-800 transition-all">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endif

                @if(session('error') || $errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200/80 shadow-xs text-rose-800">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-semibold">{{ session('error') ?? 'Terdapat beberapa kesalahan input data:' }}</p>
                            @if($errors->any())
                            <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-rose-700">
                                @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Yielded View Content -->
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
