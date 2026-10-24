# PRD: Sistem Inventory Gudang

Penulis: Satria Nugraha Saputra (Darma) · Status: Draft v0.1 · Tanggal: 17 Oktober 2026 · Jenis proyek: Portfolio

## 1. Ringkasan

Sistem Inventory Gudang adalah aplikasi web untuk mencatat, memantau, dan melaporkan stok barang di gudang secara akurat dan real-time. Sistem mencakup pengelolaan data barang, penerimaan barang dari supplier, pengeluaran barang, pemantauan stok, penyesuaian stok (stock opname), dan pelaporan.

Proyek ini dibuat sebagai portfolio, sehingga selain berfungsi dengan benar, sistem harus rapi secara arsitektur, terdokumentasi, bisa didemokan secara online, dan mencerminkan praktik pengembangan perangkat lunak yang baik.

## 2. Latar Belakang dan Masalah

Pencatatan stok gudang secara manual (buku, spreadsheet terpisah) menimbulkan masalah yang umum terjadi:

- Stok di catatan tidak sama dengan stok fisik.
- Sulit mengetahui sisa barang saat dibutuhkan.
- Barang habis tanpa peringatan, atau menumpuk berlebihan.
- Tidak ada jejak siapa yang mengubah data.
- Laporan harus disusun manual dan memakan waktu.

Sistem ini menjawab masalah tersebut dengan pencatatan terpusat berbasis transaksi.

## 3. Tujuan dan Indikator Keberhasilan

### Tujuan produk

1. Menyediakan data stok yang akurat dan selalu terbaru.
2. Mempercepat pencatatan barang masuk dan keluar.
3. Memberi peringatan dini saat stok menipis.
4. Menyediakan jejak audit untuk setiap perubahan stok.
5. Menghasilkan laporan stok dan mutasi dengan cepat.

### Tujuan portfolio

1. Menunjukkan kemampuan merancang database dan logika bisnis yang benar.
2. Menunjukkan kemampuan membangun REST API dan antarmuka yang rapi.
3. Memiliki live demo, repositori GitHub yang bersih, dan README yang lengkap.

### Indikator keberhasilan

| Indikator | Target |
| --- | --- |
| Stok tidak pernah bernilai negatif | 100% (dijamin validasi dan test) |
| Setiap perubahan stok tercatat di riwayat mutasi | 100% |
| Waktu mencatat satu transaksi barang masuk/keluar | Kurang dari 1 menit |
| Cakupan test untuk logika stok | Minimal 80% pada modul transaksi |
| Live demo dapat diakses publik | Ya, dengan akun demo dan data contoh |
| Dokumentasi | README, ERD, dan dokumentasi API tersedia |

## 4. Ruang Lingkup

### Termasuk (In Scope)

- Autentikasi dan manajemen peran pengguna.
- Master data: barang, kategori, satuan, supplier, gudang, dan rak/lokasi.
- Transaksi barang masuk dan barang keluar.
- Perhitungan dan tampilan stok real-time.
- Peringatan stok minimum.
- Stock opname (penyesuaian stok fisik).
- Riwayat mutasi stok dan audit log.
- Dashboard dan laporan dengan ekspor Excel/PDF.
- REST API terdokumentasi.

### Fitur pembeda (dipilih sebagian, sesuai waktu)

- Scan barcode/QR melalui kamera browser.
- Multi-gudang dan transfer antar gudang.
- Grafik tren pergerakan barang.

### Tidak termasuk (Out of Scope)

- Modul keuangan, akuntansi, dan faktur pajak.
- Integrasi dengan kasir/POS atau marketplace.
- Aplikasi mobile native.
- Manajemen pesanan penjualan dan pengiriman (shipping).
- Perhitungan nilai persediaan dengan metode FIFO/LIFO penuh (dapat menjadi pengembangan lanjutan).

## 5. Pengguna dan Peran

| Peran | Deskripsi | Hak akses utama |
| --- | --- | --- |
| Admin | Mengelola sistem dan data master | Semua fitur, kelola pengguna, lihat audit log |
| Staf Gudang | Operator harian gudang | Catat barang masuk/keluar, lihat stok, stock opname (draft) |
| Manajer | Pengawas dan pengambil keputusan | Lihat dashboard dan laporan, setujui stock opname |

## 6. User Stories

- Sebagai staf gudang, saya ingin mencatat barang masuk dari supplier agar stok bertambah otomatis.
- Sebagai staf gudang, saya ingin mencatat barang keluar agar stok berkurang otomatis dan tidak bisa minus.
- Sebagai staf gudang, saya ingin mencari barang berdasarkan nama, kode, atau barcode agar cepat menemukannya.
- Sebagai staf gudang, saya ingin melakukan stock opname agar stok sistem sama dengan stok fisik.
- Sebagai manajer, saya ingin melihat dashboard stok dan barang kritis agar bisa memutuskan pembelian ulang.
- Sebagai manajer, saya ingin mengekspor laporan mutasi per periode agar bisa dibahas dalam rapat.
- Sebagai admin, saya ingin mengelola pengguna dan peran agar akses data terkontrol.
- Sebagai admin, saya ingin melihat siapa mengubah apa dan kapan agar ada akuntabilitas.

## 7. Kebutuhan Fungsional

Prioritas: **P0** wajib (MVP), **P1** penting, **P2** nilai tambah.

### 7.1 Autentikasi dan Otorisasi

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-01 | Pengguna dapat login dan logout dengan email/username dan password | P0 |
| FR-02 | Akses fitur dibatasi berdasarkan peran (Admin, Staf Gudang, Manajer) | P0 |
| FR-03 | Admin dapat menambah, mengubah, menonaktifkan pengguna | P0 |
| FR-04 | Password disimpan dengan hashing; sesi memiliki masa berlaku | P0 |

### 7.2 Master Data

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-10 | CRUD barang: kode/SKU unik, nama, kategori, satuan, harga beli, stok minimum, deskripsi, foto | P0 |
| FR-11 | CRUD kategori dan satuan | P0 |
| FR-12 | CRUD supplier: nama, kontak, alamat | P0 |
| FR-13 | CRUD gudang dan lokasi/rak | P1 |
| FR-14 | Pencarian, filter, dan pagination pada daftar master data | P0 |
| FR-15 | Barang yang sudah memiliki transaksi tidak dapat dihapus, hanya dinonaktifkan | P0 |
| FR-16 | Impor data barang dari file CSV/Excel | P2 |

### 7.3 Barang Masuk

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-20 | Membuat transaksi barang masuk dengan nomor dokumen otomatis, tanggal, supplier, dan daftar barang beserta jumlah | P0 |
| FR-21 | Stok bertambah otomatis setelah transaksi disimpan | P0 |
| FR-22 | Transaksi dapat dibatalkan (void) dengan pencatatan alasan; stok dikoreksi otomatis | P1 |
| FR-23 | Menentukan lokasi/rak penyimpanan saat barang diterima | P1 |

### 7.4 Barang Keluar

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-30 | Membuat transaksi barang keluar dengan nomor dokumen otomatis, tanggal, tujuan/penerima, dan daftar barang | P0 |
| FR-31 | Sistem menolak transaksi jika jumlah melebihi stok tersedia | P0 |
| FR-32 | Stok berkurang otomatis setelah transaksi disimpan | P0 |
| FR-33 | Transaksi dapat dibatalkan (void) dengan pencatatan alasan | P1 |

### 7.5 Stok dan Peringatan

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-40 | Menampilkan stok terkini per barang (dan per gudang jika multi-gudang) | P0 |
| FR-41 | Menampilkan kartu stok (riwayat mutasi) per barang dengan saldo berjalan | P0 |
| FR-42 | Penanda dan daftar barang dengan stok di bawah atau sama dengan stok minimum | P0 |
| FR-43 | Notifikasi stok menipis di dashboard (dan email untuk P2) | P1 |

### 7.6 Stock Opname

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-50 | Staf membuat sesi opname dan mengisi jumlah stok fisik | P1 |
| FR-51 | Sistem menghitung selisih antara stok sistem dan stok fisik | P1 |
| FR-52 | Manajer menyetujui opname; setelah disetujui, stok disesuaikan dan tercatat sebagai mutasi penyesuaian | P1 |

### 7.7 Transfer Antar Gudang (opsional)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-60 | Memindahkan stok dari satu gudang ke gudang lain dalam satu transaksi | P2 |
| FR-61 | Transfer mengurangi stok asal dan menambah stok tujuan secara atomik | P2 |

### 7.8 Barcode/QR (opsional)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-70 | Membuat dan mencetak label barcode/QR untuk setiap barang | P2 |
| FR-71 | Memindai barcode lewat kamera browser untuk mengisi barang pada transaksi | P2 |

### 7.9 Dashboard dan Laporan

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-80 | Dashboard: total jenis barang, total stok, jumlah barang kritis, transaksi terbaru | P0 |
| FR-81 | Laporan stok saat ini | P0 |
| FR-82 | Laporan mutasi (masuk/keluar) per periode, barang, dan supplier | P0 |
| FR-83 | Ekspor laporan ke Excel dan PDF | P1 |
| FR-84 | Grafik tren barang masuk/keluar dan barang paling sering keluar | P2 |

### 7.10 Audit Log

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-90 | Mencatat siapa, kapan, aksi, dan data sebelum/sesudah untuk perubahan data penting | P1 |
| FR-91 | Admin dapat melihat dan memfilter audit log | P1 |

## 8. Aturan Bisnis

1. Stok tidak boleh bernilai negatif dalam kondisi apa pun.
2. Stok adalah hasil akumulasi dari mutasi; setiap perubahan stok harus memiliki catatan mutasi.
3. Transaksi yang sudah disimpan tidak diedit langsung; koreksi dilakukan lewat pembatalan (void) atau penyesuaian.
4. Kode/SKU barang bersifat unik.
5. Barang dengan riwayat transaksi hanya dapat dinonaktifkan, tidak dihapus.
6. Setiap transaksi yang mengubah beberapa tabel harus berjalan dalam satu database transaction (semua berhasil atau semua dibatalkan).
7. Penyesuaian stok dari opname hanya berlaku setelah disetujui Manajer.

## 9. Kebutuhan Non-Fungsional

| Kategori | Kebutuhan |
| --- | --- |
| Keamanan | Hashing password, proteksi CSRF/XSS/SQL injection, validasi input di sisi server, pembatasan percobaan login |
| Performa | Halaman daftar dimuat kurang dari 2 detik pada data sekitar 10.000 barang; gunakan indeks database dan pagination |
| Keandalan | Backup database terjadwal; transaksi atomik |
| Kegunaan | Antarmuka responsif (desktop dan tablet/ponsel), navigasi jelas, pesan error yang mudah dipahami |
| Maintainability | Struktur kode berlapis (controller, service, repository/model), konsisten, dan memiliki test otomatis |
| Portabilitas | Dapat dijalankan dengan Docker atau panduan instalasi manual |
| Dokumentasi | README, ERD, dokumentasi API (Swagger/Postman), panduan akun demo |

## 10. Alur Utama

### Barang masuk

1. Staf membuka menu Barang Masuk dan memilih supplier serta tanggal.
2. Staf menambahkan barang dan jumlah (manual atau scan barcode).
3. Staf menyimpan transaksi.
4. Sistem memvalidasi data, menyimpan transaksi dan detail, mencatat mutasi, dan menambah stok dalam satu database transaction.
5. Sistem menampilkan konfirmasi dan nomor dokumen.

### Barang keluar

1. Staf membuka menu Barang Keluar dan mengisi tujuan serta tanggal.
2. Staf menambahkan barang dan jumlah.
3. Sistem memeriksa ketersediaan stok; jika kurang, transaksi ditolak dengan pesan jelas.
4. Jika cukup, sistem menyimpan transaksi, mencatat mutasi, dan mengurangi stok secara atomik.
5. Jika stok jatuh di bawah minimum, barang muncul di daftar peringatan.

### Stock opname

1. Staf membuat sesi opname dan menginput stok fisik.
2. Sistem menampilkan selisih terhadap stok sistem.
3. Manajer meninjau dan menyetujui atau menolak.
4. Jika disetujui, sistem membuat mutasi penyesuaian dan memperbarui stok.

## 11. Model Data (Garis Besar)

| Tabel | Fungsi | Kolom utama |
| --- | --- | --- |
| users | Pengguna dan peran | id, nama, email, password, role, is\_active |
| categories | Kategori barang | id, nama |
| units | Satuan | id, nama, singkatan |
| items | Master barang | id, sku, nama, category\_id, unit\_id, harga\_beli, stok\_minimum, is\_active |
| suppliers | Master supplier | id, nama, kontak, alamat |
| warehouses | Gudang | id, nama, alamat |
| locations | Rak/lokasi dalam gudang | id, warehouse\_id, kode |
| stock\_in / stock\_in\_details | Header dan detail barang masuk | no\_dokumen, tanggal, supplier\_id, user\_id; item\_id, qty |
| stock\_out / stock\_out\_details | Header dan detail barang keluar | no\_dokumen, tanggal, tujuan, user\_id; item\_id, qty |
| stock\_movements | Log semua mutasi stok | item\_id, warehouse\_id, tipe (in/out/adjust/transfer), qty, saldo\_sesudah, referensi, user\_id, created\_at |
| stock\_opnames / details | Sesi opname | tanggal, status; item\_id, qty\_sistem, qty\_fisik, selisih |
| audit\_logs | Jejak perubahan | user\_id, aksi, entitas, data\_lama, data\_baru, created\_at |

Catatan: ERD lengkap dibuat pada tahap desain dan dilampirkan di repositori.

## 12. Arsitektur dan Teknologi

- **Backend:** Laravel (PHP) dengan REST API.
- **Database:** MySQL atau PostgreSQL.
- **Frontend:** Blade + Bootstrap/Tailwind, atau frontend terpisah dengan React/Vue yang mengonsumsi API.
- **Autentikasi:** Laravel Sanctum (untuk API) atau session.
- **Ekspor laporan:** Laravel Excel dan DomPDF.
- **Dokumentasi API:** Swagger/OpenAPI atau koleksi Postman.
- **Testing:** PHPUnit/Pest untuk unit dan feature test.
- **Deployment:** Docker dan VPS/hosting, dengan CI sederhana lewat GitHub Actions.

## 13. Ringkasan Endpoint API (Contoh)

| Metode | Endpoint | Fungsi |
| --- | --- | --- |
| POST | /api/login | Login |
| GET/POST | /api/items | Daftar dan tambah barang |
| GET/PUT/DELETE | /api/items/{id} | Detail, ubah, nonaktifkan barang |
| GET/POST | /api/stock-in | Daftar dan buat barang masuk |
| GET/POST | /api/stock-out | Daftar dan buat barang keluar |
| GET | /api/stocks | Stok terkini |
| GET | /api/items/{id}/movements | Kartu stok barang |
| GET | /api/reports/stock | Laporan stok |
| GET | /api/reports/movements | Laporan mutasi |
| POST | /api/stock-opnames | Membuat sesi opname |
| POST | /api/stock-opnames/{id}/approve | Menyetujui opname |

## 14. Rencana Rilis dan Milestone

| Fase | Cakupan | Perkiraan durasi |
| --- | --- | --- |
| 1. Perencanaan | Finalisasi PRD, ERD, desain tampilan sederhana | 3-4 hari |
| 2. Fondasi | Setup proyek, autentikasi, peran, master data | 1 minggu |
| 3. Transaksi | Barang masuk/keluar, logika stok, kartu stok | 1-2 minggu |
| 4. Pelaporan | Dashboard, laporan, ekspor, peringatan stok minimum | 1 minggu |
| 5. Nilai tambah | Stock opname, audit log, dan 1-2 fitur pembeda (barcode/multi-gudang) | 1-2 minggu |
| 6. Finalisasi | Testing, perbaikan, README, dokumentasi API, deploy demo | 1 minggu |

Durasi bersifat perkiraan untuk pengerjaan paruh waktu oleh satu pengembang dan dapat disesuaikan.

## 15. Kriteria Selesai (Definition of Done)

- Semua kebutuhan P0 berfungsi dan lolos pengujian.
- Minimal dua kebutuhan P1 (misalnya stock opname dan audit log) selesai.
- Test otomatis untuk logika stok berjalan dan lulus.
- Live demo dapat diakses dengan akun demo untuk tiap peran dan data contoh realistis.
- Repositori GitHub rapi, dengan README (deskripsi, fitur, screenshot, tech stack, ERD, cara instalasi).
- Proyek dicantumkan di portfolio website dengan screenshot dan tautan demo.

## 16. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
| --- | --- | --- |
| Cakupan terlalu luas | Proyek tidak selesai | Kerjakan P0 dulu; fitur P2 hanya jika waktu tersisa |
| Stok tidak konsisten karena transaksi bersamaan | Data salah | Database transaction dan penguncian baris (row locking) |
| Data demo tidak realistis | Portfolio kurang meyakinkan | Siapkan seeder dengan data contoh yang masuk akal |
| Kurang waktu untuk testing dan dokumentasi | Kualitas rendah | Jadwalkan fase finalisasi secara khusus |

## 17. Asumsi dan Pertanyaan Terbuka

### Asumsi

- Sistem digunakan oleh satu organisasi (single-tenant).
- Satu gudang pada rilis awal; multi-gudang sebagai pengembangan lanjutan.
- Metode penilaian stok sederhana (tanpa FIFO/LIFO penuh).

### Pertanyaan terbuka

1. Apakah frontend akan dibuat terpisah (React/Vue) atau cukup Blade?
2. Fitur pembeda mana yang diprioritaskan: barcode, multi-gudang, atau grafik tren?
3. Apakah perlu pelacakan nomor batch dan tanggal kedaluwarsa?
4. Di mana demo akan di-deploy dan berapa anggarannya?
