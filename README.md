# 📦 Sistem Inventory Pergudangan (GudangPro)

> Aplikasi web manajemen stok pergudangan modern, akurat, dan real-time berbasis **Laravel 12**, **Tailwind CSS**, dan **Alpine.js**. Dirancang dengan arsitektur bersih, transaksi stok atomik (ACID), dan jaminan validasi **anti-stok negatif (100%)**.

---

## 🌟 Fitur Utama & Kebutuhan Fungsional

### 1. Autentikasi & Otorisasi Multi-Peran (FR-01 – FR-04)
- **Role-Based Access Control (RBAC)** untuk 3 peran pengguna:
  - 🛡️ **Administrator**: Akses penuh seluruh sistem, manajemen pengguna, dan riwayat audit log.
  - 📦 **Staf Gudang**: Pencatatan transaksi barang masuk, barang keluar, pemantauan stok, dan stock opname fisik.
  - 📊 **Manajer**: Pemantauan KPI dashboard, persetujuan stock opname fisik, dan laporan ekspor mutasi & valuasi.
- Quick 1-Click Demo Login pada halaman login untuk memudahkan presentasi portofolio.

### 2. Master Data Terintegrasi (FR-10 – FR-15)
- **Data Barang (Items)**: Kode SKU unik, nama barang, kategori, satuan ukur, gudang default, lokasi rak, harga beli, harga jual, stok minimum, dan saldo awal.
- **Kategori & Satuan**: Manajemen fleksibel dengan relasi integritas data.
- **Supplier / Mitra Pemasok**: Pencatatan kontak PIC, telepon, email, dan alamat vendor.
- **Fasilitas Gudang & Lokasi Rak**: Pengelompokan fisik per rak (A-01, B-02, dll).
- **Aturan Integritas Master Data**: Barang yang telah memiliki riwayat mutasi/transaksi dilindungi agar tidak dapat dihapus sembarangan, hanya dapat dinonaktifkan.

### 3. Transaksi Barang Masuk (Inbound) (FR-20 – FR-23)
- Formulir dinamis multi-baris (Alpine.js) dengan pencarian barang cepat dan kalkulasi subtotal otomatis.
- Penomoran dokumen otomatis (`IN-YYYYMMDD-XXXX`).
- Penambahan saldo stok fisik secara atomik dibungkus dalam `DB::transaction`.
- Format Bukti Penerimaan Barang resmi siap cetak (*Printable Invoice / Delivery Order*).
- Fitur **Pembatalan (Void)** dengan alasan audit: otomatis mengoreksi kembali saldo stok fisik.

### 4. Transaksi Barang Keluar (Outbound) (FR-30 – FR-33)
- Validasi ketat ketersediaan stok fisik (*row locking with `lockForUpdate`*).
- **Jaminan 100% Anti-Stok Negatif**: Transaksi otomatis ditolak jika jumlah pengeluaran melebihi sisa stok di gudang.
- Formulir interaktif dengan indikator sisa stok dan peringatan langsung di peramban.
- Format Surat Jalan / Bukti Pengeluaran Barang siap cetak.
- Fitur **Pembatalan (Void)** dengan alasan audit: otomatis mengembalikan saldo stok ke gudang.

### 5. Kartu Stok & Saldo Berjalan (Running Balance) (FR-40, FR-41)
- Setiap pergerakan stok dicatat dengan `balance_before`, `quantity`, dan `balance_after`.
- Halaman **Kartu Stok Resmi** per barang dengan format buku besar mutasi (Masuk, Keluar, Saldo Akhir).
- Filter mutasi berdasarkan rentang tanggal, jenis mutasi, dan no. referensi dokumen.

### 6. Peringatan Dini Stok Minimum & Dashboard KPI (FR-42, FR-80)
- Indikator status visual:
  - 🟢 **Aman**: Stok di atas batas minimum.
  - 🟡 **Kritis**: Stok berada pada atau di bawah batas minimum (`current_stock <= min_stock`).
  - 🔴 **Habis**: Stok fisik bernilai 0 unit.
- Banner peringatan dinamis di dashboard untuk item yang membutuhkan pemesanan ulang (*restock*).
- Metrik real-time: Total Jenis Barang, Total Unit Fisik, Valuasi Aset Persediaan, dan Arus Masuk/Keluar Hari Ini.

### 7. Stock Opname Fisik Gudang (FR-50 – FR-52)
- Sesi pencatatan fisik oleh Staf Gudang dengan perhitungan selisih (*difference*) real-time terhadap stok sistem.
- Alur *approval* berjenjang: Manajer / Admin menyetujui hasil opname.
- Begitu disetujui, saldo stok otomatis disesuaikan dan dicatat sebagai mutasi penyesuaian (*adjustment*) di kartu stok.

### 8. Laporan & Ekspor Data (FR-81 – FR-83)
- Laporan Posisi Stok Gudang & Valuasi Persediaan.
- Laporan Mutasi Barang per Periode (Masuk & Keluar).
- Ekspor satu klik ke format **CSV / Excel** dan tampilan cetak (*print-friendly*).

### 9. Jejak Audit (Audit Log) (FR-90, FR-91)
- Pencatatan seluruh aksi penting (login, buat transaksi, batalkan transaksi, persetujuan opname, perubahan master data) lengkap dengan timestamp dan IP address.

---

## 🛠️ Tech Stack & Arsitektur

- **Backend:** Laravel 12 (PHP 8.2+)
- **Database:** SQLite (zero-config, portable) / Kompatibel penuh MySQL/PostgreSQL
- **Frontend:** Blade Templates + Tailwind CSS + Alpine.js
- **Typography:** Plus Jakarta Sans & JetBrains Mono (Google Fonts)
- **Testing:** PHPUnit (Unit & Feature Tests dengan 100% *pass rate* pada modul transaksi stok)

---

## 📁 Struktur Database (Entity Relationship)

```text
users (id, name, email, password, role, is_active)
  ├── stock_ins (id, document_number, date, supplier_id, warehouse_id, user_id, status)
  │     └── stock_in_details (id, stock_in_id, item_id, quantity, unit_price, subtotal)
  ├── stock_outs (id, document_number, date, recipient, warehouse_id, user_id, status)
  │     └── stock_out_details (id, stock_out_id, item_id, quantity)
  ├── stock_opnames (id, document_number, date, warehouse_id, user_id, status, approved_by)
  │     └── stock_opname_details (id, stock_opname_id, item_id, system_stock, physical_stock, difference)
  └── stock_movements (id, item_id, warehouse_id, user_id, type, quantity, balance_before, balance_after, reference_number)

categories (id, name, code, description) ──┐
units (id, name, symbol) ───────────────────┼──> items (id, sku, name, current_stock, min_stock, purchase_price)
warehouses (id, name, code, address) ───────┤
  └── locations (id, warehouse_id, code) ───┘
```

---

## 🚀 Panduan Instalasi Lokal

### 1. Kebutuhan Sistem
- PHP 8.2 atau lebih tinggi dengan ekstensi `pdo_sqlite`, `bcmath`, `curl`, `mbstring`
- Composer 2.x
- Node.js v18+ & NPM

### 2. Langkah Menjalankan Aplikasi
```bash
# 1. Kloning atau buka direktori proyek
cd "System Inventory"

# 2. Instal dependensi PHP
composer install

# 3. Instal dependensi Node.js & build frontend
npm install
npm run build

# 4. Siapkan berkas environment (jika belum ada)
copy .env.example .env
php artisan key:generate

# 5. Jalankan migrasi dan isi data demo awal
php artisan migrate:fresh --seed

# 6. Jalankan server lokal
php artisan serve
```

Akses sistem di peramban Anda melalui URL: **`http://127.0.0.1:8000`**

---

## 🔑 Akun Uji Coba (Demo Credentials)

| Peran (Role) | Alamat Email | Kata Sandi | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@inventory.local` | `password` | Semua fitur, kelola pengguna, audit log |
| **Staf Gudang** | `staff@inventory.local` | `password` | Input barang masuk & keluar, input stock opname |
| **Manajer Operasional** | `manager@inventory.local` | `password` | Dashboard eksekutif, laporan mutasi, persetujuan opname |

> **Catatan:** Pada halaman login telah disediakan tombol cepat 1-klik untuk otomatis mengisi akun di atas tanpa perlu mengetik manual.

---

## 🧪 Pengujian Otomatis (Automated Testing)

Untuk menjalankan seluruh rangkaian pengujian fitur dan logika transaksi anti-stok negatif:

```bash
php artisan test
```

Semua pengujian (`InventoryTransactionTest`, `AuthAndAccessTest`, `ExampleTest`) terkonfirmasi lulus (*passed*).
