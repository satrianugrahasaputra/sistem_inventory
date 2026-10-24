@extends('layouts.app')

@section('title', 'Master Data Supplier')
@section('page_title', 'Supplier / Mitra')

@section('content')
<div class="space-y-6" x-data="{
    modalOpen: false,
    isEdit: false,
    formAction: '{{ route('suppliers.store') }}',
    formData: { name: '', contact_person: '', phone: '', email: '', address: '' },
    openAdd() {
        this.isEdit = false;
        this.formAction = '{{ route('suppliers.store') }}';
        this.formData = { name: '', contact_person: '', phone: '', email: '', address: '' };
        this.modalOpen = true;
    },
    openEdit(s) {
        this.isEdit = true;
        this.formAction = '/suppliers/' + s.id;
        this.formData = { name: s.name, contact_person: s.contact_person || '', phone: s.phone || '', email: s.email || '', address: s.address || '' };
        this.modalOpen = true;
    }
}">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Mitra Supplier & Pemasok (FR-12)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data vendor, kontak penanggung jawab, dan riwayat penerimaan barang</p>
        </div>
        <button type="button" @click="openAdd()" id="btn-add-supplier" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Supplier Baru
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs max-w-lg">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama vendor, kontak, email..."
                class="flex-1 px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold">Cari</button>
        </form>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">Nama Perusahaan / Supplier</th>
                        <th class="py-3 px-4 font-semibold">Kontak PIC</th>
                        <th class="py-3 px-4 font-semibold">Telepon & Email</th>
                        <th class="py-3 px-4 font-semibold">Alamat Kantor / Gudang</th>
                        <th class="py-3 px-4 font-semibold text-center">Total Transaksi Masuk</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($suppliers as $s)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3 px-4 font-bold text-slate-900">{{ $s->name }}</td>
                        <td class="py-3 px-4 text-slate-700">{{ $s->contact_person ?? '-' }}</td>
                        <td class="py-3 px-4 text-slate-600">
                            <div>{{ $s->phone ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $s->email ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 text-slate-500 max-w-[250px] truncate" title="{{ $s->address }}">
                            {{ $s->address ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-slate-700">
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-800">
                                {{ $s->stock_ins_count }} Transaksi
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <button type="button" @click="openEdit({{ $s }})" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('suppliers.destroy', $s) }}" onsubmit="return confirm('Hapus supplier ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400">Belum ada data supplier.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Form Tambah/Ubah Supplier -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900" x-text="isEdit ? 'Ubah Data Supplier' : 'Tambah Supplier Baru'"></h3>
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
                    <label for="s_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / Supplier <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="s_name" x-model="formData.name" required placeholder="Contoh: PT Sumber Berkah..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="s_pic" class="block text-xs font-semibold text-slate-700 mb-1">Kontak PIC</label>
                        <input type="text" name="contact_person" id="s_pic" x-model="formData.contact_person" placeholder="Nama PIC..."
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="s_phone" class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / WA</label>
                        <input type="text" name="phone" id="s_phone" x-model="formData.phone" placeholder="08..."
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="s_email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" id="s_email" x-model="formData.email" placeholder="sales@vendor.com"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="s_addr" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor / Gudang</label>
                    <textarea name="address" id="s_addr" x-model="formData.address" rows="2"
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
