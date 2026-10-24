@extends('layouts.app')

@section('title', 'Catat Pengeluaran Barang Keluar')
@section('page_title', 'Barang Keluar Baru')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{
    catalog: {{ Js::from($items) }},
    rows: [
        { item_id: '', quantity: 1, available_stock: 0, unit: '', notes: '', exceeds: false }
    ],
    addRow() {
        this.rows.push({ item_id: '', quantity: 1, available_stock: 0, unit: '', notes: '', exceeds: false });
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
            this.rows[index].available_stock = parseInt(found.current_stock) || 0;
            this.rows[index].unit = found.unit ? found.unit.symbol : 'Unit';
            this.validateStock(index);
        } else {
            this.rows[index].available_stock = 0;
            this.rows[index].unit = '';
            this.rows[index].exceeds = false;
        }
    },
    validateStock(index) {
        const qty = parseInt(this.rows[index].quantity) || 0;
        const available = this.rows[index].available_stock;
        this.rows[index].exceeds = qty > available && this.rows[index].item_id !== '';
    },
    hasExceededStock() {
        return this.rows.some(r => r.exceeds);
    },
    totalQuantity() {
        return this.rows.reduce((sum, r) => sum + (parseInt(r.quantity) || 0), 0);
    }
}">

    <div class="flex items-center justify-between">
        <a href="{{ route('stock-outs.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Transaksi Keluar
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Formulir Pengeluaran Barang Keluar (Outbound)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Sistem memverifikasi ketersediaan stok secara atomik dan menolak transaksi jika melebihi stok.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                Anti-Negative Stock Protected
            </span>
        </div>

        <form method="POST" action="{{ route('stock-outs.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Header Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Document Number -->
                <div>
                    <label for="document_number" class="block text-xs font-semibold text-slate-700 mb-1">
                        No. Dokumen / Surat Jalan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="document_number" id="document_number" value="{{ old('document_number', $suggestedDocNumber) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <!-- Date -->
                <div>
                    <label for="date" class="block text-xs font-semibold text-slate-700 mb-1">
                        Tanggal Pengeluaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date" id="date" value="{{ old('date', now()->toDateString()) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <!-- Recipient / Purpose -->
                <div>
                    <label for="recipient" class="block text-xs font-semibold text-slate-700 mb-1">
                        Tujuan / Penerima <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="recipient" id="recipient" value="{{ old('recipient') }}" required placeholder="Contoh: Divisi Pemeliharaan Line 2"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <!-- Warehouse Source -->
                <div>
                    <label for="warehouse_id" class="block text-xs font-semibold text-slate-700 mb-1">Gudang Asal</label>
                    <select name="warehouse_id" id="warehouse_id"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <option value="">-- Gudang Standar --</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }} ({{ $wh->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Keperluan Pengeluaran</label>
                <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Contoh: Permintaan penggantian part darurat mesin stamping line 2"
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <!-- Dynamic Items Rows Table -->
            <div class="pt-4 border-t border-slate-200">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Barang yang Dikeluarkan</h3>
                        <p class="text-xs text-slate-500">Pilih barang dan masukkan kuantiti (tidak boleh melebihi stok tersedia)</p>
                    </div>
                    <button type="button" @click="addRow()" id="btn-add-out-row" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        + Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 px-3 font-semibold w-1/2">Barang (SKU - Nama)</th>
                                <th class="py-2.5 px-3 font-semibold text-center w-28">Stok Tersedia</th>
                                <th class="py-2.5 px-3 font-semibold text-center w-28">Jumlah Keluar</th>
                                <th class="py-2.5 px-3 font-semibold w-16 text-center">Satuan</th>
                                <th class="py-2.5 px-3 font-semibold w-40">Catatan Item</th>
                                <th class="py-2.5 px-3 font-semibold text-center w-12">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(row, index) in rows" :key="index">
                                <tr class="hover:bg-slate-50" :class="{ 'bg-rose-50/60': row.exceeds }">
                                    <!-- Select Item -->
                                    <td class="p-2.5">
                                        <select :name="'items[' + index + '][item_id]'" x-model="row.item_id" @change="onItemChange(index)" required
                                            class="w-full py-1.5 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <option value="">-- Pilih Barang Tersedia --</option>
                                            <template x-for="item in catalog" :key="item.id">
                                                <option :value="item.id" x-text="item.sku + ' - ' + item.name + ' (Sisa: ' + item.current_stock + ' ' + (item.unit ? item.unit.symbol : '') + ')'"></option>
                                            </template>
                                        </select>
                                        <p x-show="row.exceeds" class="text-[10px] text-rose-600 font-bold mt-1">
                                            &times; Jumlah diminta melebihi sisa stok fisik (<span x-text="row.available_stock"></span>)!
                                        </p>
                                    </td>

                                    <!-- Available Stock Badge -->
                                    <td class="p-2.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold"
                                            :class="row.available_stock > 0 ? 'bg-slate-100 text-slate-800' : 'bg-rose-100 text-rose-800'"
                                            x-text="row.available_stock"></span>
                                    </td>

                                    <!-- Quantity Input -->
                                    <td class="p-2.5">
                                        <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="row.quantity" @input="validateStock(index)" min="1" required
                                            class="w-full py-1.5 px-2 bg-white border rounded-lg text-xs font-bold text-center text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            :class="row.exceeds ? 'border-rose-500 bg-rose-50' : 'border-slate-300'">
                                    </td>

                                    <!-- Unit Symbol -->
                                    <td class="p-2.5 text-center text-slate-500 font-semibold" x-text="row.unit || '-'"></td>

                                    <!-- Notes -->
                                    <td class="p-2.5">
                                        <input type="text" :name="'items[' + index + '][notes]'" x-model="row.notes" placeholder="Ket..."
                                            class="w-full py-1.5 px-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700">
                                    </td>

                                    <!-- Remove Action -->
                                    <td class="p-2.5 text-center">
                                        <button type="button" @click="removeRow(index)" class="p-1 rounded text-rose-500 hover:bg-rose-50 transition-colors" title="Hapus Baris">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Total Summary Bar -->
                <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center space-x-6 text-xs text-slate-600">
                        <div>
                            <span>Total Baris Barang:</span>
                            <strong class="text-slate-900 ml-1 font-bold" x-text="rows.length"></strong>
                        </div>
                        <div>
                            <span>Total Kuantitas Keluar:</span>
                            <strong class="text-rose-600 ml-1 font-extrabold text-sm" x-text="totalQuantity() + ' Unit'"></strong>
                        </div>
                    </div>

                    <div x-show="hasExceededStock()" class="text-xs text-rose-600 font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Ada baris yang melebihi stok! Perbaiki sebelum menyimpan.</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-end space-x-3">
                <a href="{{ route('stock-outs.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" id="btn-save-stock-out" :disabled="hasExceededStock()"
                    class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-semibold shadow-sm shadow-rose-600/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Pengeluaran Barang
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
