<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem Inventory Gudang</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased bg-radial from-slate-900 via-slate-950 to-black text-slate-100">

    <div class="w-full max-w-md" x-data="{
        email: '{{ old('email', 'admin@inventory.local') }}',
        password: 'password',
        fillAccount(roleEmail) {
            this.email = roleEmail;
            this.password = 'password';
        }
    }">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-violet-400 items-center justify-center text-white shadow-xl shadow-indigo-500/25 mb-4 ring-4 ring-indigo-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Sistem Inventory Gudang</h1>
            <p class="text-xs text-slate-400 mt-1">Platform Manajemen Stok, Mutasi & Logistik Real-Time</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl p-7 sm:p-8">
            @if(session('info'))
            <div class="mb-5 p-3 rounded-lg bg-indigo-950/60 border border-indigo-800/60 text-xs text-indigo-300">
                {{ session('info') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-rose-950/60 border border-rose-800/80 text-rose-300 text-xs">
                <p class="font-semibold mb-1">Gagal Masuk:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <input id="email" type="email" name="email" x-model="email" required autocomplete="email" autofocus
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="nama@inventory.local">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" x-model="password" required autocomplete="current-password"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center text-xs text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2">Ingat sesi saya</span>
                    </label>
                </div>

                <button type="submit" id="btn-login" class="w-full py-2.5 px-4 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                    <span>Masuk ke Dashboard</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <!-- 1-Click Demo Accounts Selector -->
            <div class="mt-7 pt-5 border-t border-slate-800">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Akun Demo (Klik untuk Isi Cepat):</p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="fillAccount('admin@inventory.local')" class="px-2 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-center transition-all group">
                        <p class="text-xs font-bold text-indigo-400 group-hover:text-indigo-300">Admin</p>
                        <p class="text-[10px] text-slate-400">Semua Akses</p>
                    </button>
                    <button type="button" @click="fillAccount('staff@inventory.local')" class="px-2 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-center transition-all group">
                        <p class="text-xs font-bold text-emerald-400 group-hover:text-emerald-300">Staf Gudang</p>
                        <p class="text-[10px] text-slate-400">Transaksi</p>
                    </button>
                    <button type="button" @click="fillAccount('manager@inventory.local')" class="px-2 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-center transition-all group">
                        <p class="text-xs font-bold text-blue-400 group-hover:text-blue-300">Manajer</p>
                        <p class="text-[10px] text-slate-400">Laporan & Opname</p>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-slate-500 mt-6">
            Sistem Inventory Gudang &copy; {{ date('Y') }} · Proyek Portofolio
        </p>
    </div>

</body>
</html>
