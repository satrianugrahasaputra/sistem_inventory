@extends('layouts.app')

@section('title', 'Manajemen Pengguna Sistem')
@section('page_title', 'Kelola Pengguna')

@section('content')
<div class="space-y-6" x-data="{
    modalOpen: false,
    isEdit: false,
    formAction: '{{ route('users.store') }}',
    formData: { name: '', email: '', role: 'staff', password: '' },
    openAdd() {
        this.isEdit = false;
        this.formAction = '{{ route('users.store') }}';
        this.formData = { name: '', email: '', role: 'staff', password: '' };
        this.modalOpen = true;
    },
    openEdit(u) {
        this.isEdit = true;
        this.formAction = '/users/' + u.id;
        this.formData = { name: u.name, email: u.email, role: u.role, password: '' };
        this.modalOpen = true;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Akses & Pengguna (FR-03)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akun dan hak akses: Admin, Staf Gudang, dan Manajer</p>
        </div>
        <button type="button" @click="openAdd()" id="btn-add-user" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengguna Baru
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">Nama Pengguna</th>
                        <th class="py-3 px-4 font-semibold">Alamat Email</th>
                        <th class="py-3 px-4 font-semibold text-center">Peran (Role)</th>
                        <th class="py-3 px-4 font-semibold text-center">Status Akun</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($users as $u)
                    <tr class="hover:bg-slate-50/60 {{ !$u->is_active ? 'opacity-50 bg-slate-50/40' : '' }}">
                        <td class="py-3 px-4 font-bold text-slate-900">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <span>{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $u->email }}</td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($u->role === 'admin')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                Administrator
                            </span>
                            @elseif($u->role === 'manager')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                Manajer Operasional
                            </span>
                            @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Staf Gudang
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($u->is_active)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-1.5">
                                <button type="button" @click="openEdit({{ $u }})" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('users.toggle-active', $u) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        @if($u->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah/Ubah User -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100" x-text="isEdit ? 'Ubah Pengguna' : 'Tambah Pengguna Baru'"></h3>
            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label for="u_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="u_name" x-model="formData.name" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="u_email" class="block text-xs font-semibold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="u_email" x-model="formData.email" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="u_role" class="block text-xs font-semibold text-slate-700 mb-1">Peran Hak Akses <span class="text-rose-500">*</span></label>
                    <select name="role" id="u_role" x-model="formData.role" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="staff">Staf Gudang (Operator Transaksi)</option>
                        <option value="manager">Manajer (Pengawas Laporan & Opname)</option>
                        <option value="admin">Administrator (Akses Penuh)</option>
                    </select>
                </div>

                <div>
                    <label for="u_pwd" class="block text-xs font-semibold text-slate-700 mb-1">
                        Kata Sandi <span x-show="!isEdit" class="text-rose-500">*</span>
                        <span x-show="isEdit" class="text-[10px] text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password" id="u_pwd" :required="!isEdit" minlength="6"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
