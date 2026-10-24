@extends('layouts.app')

@section('title', 'Satuan Ukur Barang')
@section('page_title', 'Satuan Barang')

@section('content')
<div class="space-y-6" x-data="{
    modalOpen: false,
    isEdit: false,
    formAction: '{{ route('units.store') }}',
    formData: { name: '', symbol: '' },
    openAdd() {
        this.isEdit = false;
        this.formAction = '{{ route('units.store') }}';
        this.formData = { name: '', symbol: '' };
        this.modalOpen = true;
    },
    openEdit(u) {
        this.isEdit = true;
        this.formAction = '/units/' + u.id;
        this.formData = { name: u.name, symbol: u.symbol };
        this.modalOpen = true;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Satuan Ukur Barang (FR-11)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola satuan standar stok seperti Pcs, Box, Kg, Set, Roll</p>
        </div>
        <button type="button" @click="openAdd()" id="btn-add-unit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Satuan
        </button>
    </div>

    <!-- Units Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden max-w-3xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">Simbol / Singkatan</th>
                        <th class="py-3 px-4 font-semibold">Nama Satuan Lengkap</th>
                        <th class="py-3 px-4 font-semibold text-center">Jumlah Barang Terkait</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($units as $u)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3 px-4 font-bold text-slate-900">
                            <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100 font-mono">
                                {{ $u->symbol }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-800">{{ $u->name }}</td>
                        <td class="py-3 px-4 text-center font-bold text-slate-600">{{ $u->items_count }}</td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <button type="button" @click="openEdit({{ $u }})" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('units.destroy', $u) }}" onsubmit="return confirm('Hapus satuan ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah/Ubah Satuan -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900" x-text="isEdit ? 'Ubah Satuan' : 'Tambah Satuan Baru'"></h3>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label for="u_symbol" class="block text-xs font-semibold text-slate-700 mb-1">Simbol / Singkatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="symbol" id="u_symbol" x-model="formData.symbol" required placeholder="Contoh: Pcs, Box, Kg..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="u_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Satuan Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="u_name" x-model="formData.name" required placeholder="Contoh: Pieces, Kilogram..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
