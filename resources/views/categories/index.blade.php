@extends('layouts.app')

@section('title', 'Kategori Barang')
@section('page_title', 'Kategori Barang')

@section('content')
<div class="space-y-6" x-data="{
    modalOpen: false,
    isEdit: false,
    formAction: '{{ route('categories.store') }}',
    formData: { name: '', code: '', description: '' },
    openAdd() {
        this.isEdit = false;
        this.formAction = '{{ route('categories.store') }}';
        this.formData = { name: '', code: '', description: '' };
        this.modalOpen = true;
    },
    openEdit(cat) {
        this.isEdit = true;
        this.formAction = '/categories/' + cat.id;
        this.formData = { name: cat.name, code: cat.code || '', description: cat.description || '' };
        this.modalOpen = true;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Kategori Barang (FR-11)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelompokkan barang untuk mempermudah klasifikasi dan filter</p>
        </div>
        <button type="button" @click="openAdd()" id="btn-add-category" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori
        </button>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $category)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 hover:shadow-md transition-shadow flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $category->code ?? 'KAT' }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">
                        {{ $category->items_count }} Barang
                    </span>
                </div>
                <h3 class="text-base font-bold text-slate-900">{{ $category->name }}</h3>
                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $category->description ?? 'Tidak ada keterangan khusus.' }}</p>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('items.index', ['category_id' => $category->id]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Lihat Barang &rarr;
                </a>
                <div class="flex items-center space-x-1">
                    <button type="button" @click="openEdit({{ $category }})" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ubah">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Form Tambah/Ubah Kategori -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900" x-text="isEdit ? 'Ubah Kategori' : 'Tambah Kategori Baru'"></h3>
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
                    <label for="cat_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="cat_name" x-model="formData.name" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="cat_code" class="block text-xs font-semibold text-slate-700 mb-1">Kode Singkat (Opsional)</label>
                    <input type="text" name="code" id="cat_code" x-model="formData.code" placeholder="Contoh: ELEK, TOOL..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono uppercase text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="cat_desc" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="cat_desc" x-model="formData.description" rows="2"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
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
