@extends('layouts.app')

@section('title', 'Master Gudang & Rak Lokasi')
@section('page_title', 'Gudang & Rak')

@section('content')
<div class="space-y-6" x-data="{
    whModalOpen: false,
    locModalOpen: false,
    selectedWhId: null,
    selectedWhName: '',
    openAddLoc(whId, whName) {
        this.selectedWhId = whId;
        this.selectedWhName = whName;
        this.locModalOpen = true;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Gudang & Rak / Lokasi (FR-13)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola fasilitas penyimpanan fisik dan zonasi rak barang</p>
        </div>
        <button type="button" @click="whModalOpen = true" id="btn-add-warehouse" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Fasilitas Gudang
        </button>
    </div>

    <!-- Warehouses Grid -->
    <div class="space-y-6">
        @foreach($warehouses as $wh)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-slate-900">{{ $wh->name }}</h3>
                            <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">{{ $wh->code }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $wh->address ?? 'Alamat belum disetel' }} · <strong>{{ $wh->items_count }}</strong> jenis barang tersimpan</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="openAddLoc({{ $wh->id }}, '{{ $wh->name }}')" class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Rak
                    </button>
                </div>
            </div>

            <!-- Locations / Shelves inside this Warehouse -->
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Daftar Rak / Lokasi Penyimpanan:</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                    @forelse($wh->locations as $loc)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between group">
                        <div>
                            <span class="font-mono font-bold text-xs text-indigo-700 block">{{ $loc->code }}</span>
                            <span class="text-[10px] text-slate-500 truncate block max-w-[100px]">{{ $loc->name ?? 'Rak' }}</span>
                        </div>
                        <form method="POST" action="{{ route('warehouses.locations.destroy', $loc) }}" onsubmit="return confirm('Hapus rak ini?')" class="opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-rose-600 p-1" title="Hapus rak">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="col-span-full py-4 text-center text-xs text-slate-400">
                        Belum ada rak/lokasi terdaftar di gudang ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Tambah Gudang -->
    <div x-show="whModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.away="whModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">Tambah Gudang Baru</h3>
            <form action="{{ route('warehouses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="w_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Fasilitas Gudang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="w_name" required placeholder="Contoh: Gudang Barat Sentul..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="w_code" class="block text-xs font-semibold text-slate-700 mb-1">Kode Gudang <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="w_code" required placeholder="Contoh: WH-STL..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono uppercase text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="w_addr" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Gudang</label>
                    <textarea name="address" id="w_addr" rows="2"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="whModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">Simpan Gudang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Rak / Lokasi -->
    <div x-show="locModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.away="locModalOpen = false" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                Tambah Rak di <span x-text="selectedWhName"></span>
            </h3>
            <form :action="'/warehouses/' + selectedWhId + '/locations'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="l_code" class="block text-xs font-semibold text-slate-700 mb-1">Kode Rak / Barcode <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="l_code" required placeholder="Contoh: A-01, B-03..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono uppercase text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="l_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama / Keterangan Rak</label>
                    <input type="text" name="name" id="l_name" placeholder="Contoh: Rak Lantai 1 Dekat Pintu Masuk"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="locModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">Simpan Rak</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
