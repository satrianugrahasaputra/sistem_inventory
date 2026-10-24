@extends('layouts.app')

@section('title', 'Buat Sesi Stock Opname')
@section('page_title', 'Sesi Opname Baru')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{
    catalog: {{ Js::from($items) }},
    rows: [
        { item_id: '', system_stock: 0, physical_stock: 0, diff: 0, unit: '', notes: '' }
    ],
    addRow() {
        this.rows.push({ item_id: '', system_stock: 0, physical_stock: 0, diff: 0, unit: '', notes: '' });
    },
    removeRow(index) {
        if (this.rows.length > 1) {
            this.rows.splice(index, 1);
        }
    },
    onItemChange(index) {
        const selectedId = parseInt(this.rows[index].item_id);
        const found = this.catalog.find(i => i.id === selectedId);
        if (found) {
            this.rows[index].system_stock = parseInt(found.current_stock) || 0;
            this.rows[index].physical_stock = parseInt(found.current_stock) || 0;
            this.rows[index].unit = found.unit ? found.unit.symbol : 'Unit';
            this.calcDiff(index);
        } else {
            this.rows[index].system_stock = 0;
            this.rows[index].physical_stock = 0;
            this.rows[index].diff = 0;
            this.rows[index].unit = '';
        }
    },
    calcDiff(index) {
        const sys = parseInt(this.rows[index].system_stock) || 0;
        const phys = parseInt(this.rows[index].physical_stock) || 0;
        this.rows[index].diff = phys - sys;
    }
}">

    <div class="flex items-center justify-between">
        <a href="{{ route('stock-opnames.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Sesi Opname
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-900">Formulir Sesi Stock Opname Fisik (FR-50)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Input hasil hitung fisik di gudang. Penyesuaian stok baru akan diterapkan setelah disetujui Manajer.</p>
        </div>

        <form method="POST" action="{{ route('stock-opnames.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Header Info -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="document_number" class="block text-xs font-semibold text-slate-700 mb-1">No. Dokumen Opname <span class="text-rose-500">*</span></label>
                    <input type="text" name="document_number" id="document_number" value="{{ old('document_number', $suggestedDocNumber) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pemeriksaan <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', now()->toDateString()) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="warehouse_id" class="block text-xs font-semibold text-slate-700 mb-1">Gudang yang Diperiksa</label>
                    <select name="warehouse_id" id="warehouse_id"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Fasilitas Gudang --</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Alasan Opname</label>
                <input type="text" name="notes" id="notes" placeholder="Contoh: Stock Opname Rutin Akhir Bulan..."
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Dynamic Items Rows Table -->
            <div class="pt-4 border-t border-slate-200">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Barang yang Diopname</h3>
                        <p class="text-xs text-slate-500">Bandingkan stok sistem dan input stok aktual fisik di lapangan</p>
                    </div>
                    <button type="button" @click="addRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        + Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 px-3 font-semibold w-1/3">Barang (SKU - Nama)</th>
                                <th class="py-2.5 px-3 font-semibold text-center w-28">Stok Sistem</th>
                                <th class="py-2.5 px-3 font-semibold text-center w-32">Stok Fisik Aktual</th>
                                <th class="py-2.5 px-3 font-semibold text-center w-28">Selisih</th>
                                <th class="py-2.5 px-3 font-semibold w-40">Catatan</th>
                                <th class="py-2.5 px-3 font-semibold text-center w-12">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <template x-for="(row, index) in rows" :key="index">
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2.5">
                                        <select :name="'items[' + index + '][item_id]'" x-model="row.item_id" @change="onItemChange(index)" required
                                            class="w-full py-1.5 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900">
                                            <option value="">-- Pilih Barang --</option>
                                            <template x-for="item in catalog" :key="item.id">
                                                <option :value="item.id" x-text="item.sku + ' - ' + item.name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="p-2.5 text-center font-bold text-slate-600" x-text="row.system_stock + ' ' + row.unit"></td>
                                    <td class="p-2.5">
                                        <input type="number" :name="'items[' + index + '][physical_stock]'" x-model.number="row.physical_stock" @input="calcDiff(index)" min="0" required
                                            class="w-full py-1.5 px-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-center text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-2.5 text-center font-extrabold"
                                        :class="row.diff === 0 ? 'text-slate-500' : (row.diff > 0 ? 'text-emerald-600' : 'text-rose-600')"
                                        x-text="(row.diff > 0 ? '+' : '') + row.diff"></td>
                                    <td class="p-2.5">
                                        <input type="text" :name="'items[' + index + '][notes]'" x-model="row.notes" placeholder="Catatan..."
                                            class="w-full py-1.5 px-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700">
                                    </td>
                                    <td class="p-2.5 text-center">
                                        <button type="button" @click="removeRow(index)" class="p-1 rounded text-rose-500 hover:bg-rose-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end space-x-3">
                <a href="{{ route('stock-opnames.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" id="btn-save-opname" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-xs">
                    Simpan Sesi Opname (Draft)
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
