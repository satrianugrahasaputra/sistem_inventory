<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\StockMovement;
use App\Models\StockOut;
use App\Models\StockOutDetail;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna (FR-01, FR-02)
        $admin = User::create([
            'name' => 'Administrator Gudang',
            'email' => 'admin@inventory.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $staff = User::create([
            'name' => 'Budi Santoso (Staf Gudang)',
            'email' => 'staff@inventory.local',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $manager = User::create([
            'name' => 'Siti Rahma (Manajer Operasional)',
            'email' => 'manager@inventory.local',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        // 2. Kategori Barang (FR-11)
        $catElectronics = Category::create(['name' => 'Elektronik & Perangkat', 'code' => 'ELEK', 'description' => 'Komponen elektronika, sensor, kabel, adaptor']);
        $catTools = Category::create(['name' => 'Perkakas & Alat Berat', 'code' => 'TOOL', 'description' => 'Obeng, kunci pas, bor listrik, perkakas tangan']);
        $catSafety = Category::create(['name' => 'Alat Pelindung Diri (APD)', 'code' => 'APD', 'description' => 'Helm proyek, sarung tangan, kacamata safety, rompi']);
        $catSparepart = Category::create(['name' => 'Suku Cadang Mesin', 'code' => 'SPARE', 'description' => 'Bearing, vanbelt, baut industri, gear']);
        $catOffice = Category::create(['name' => 'Perlengkapan Kantor & Packing', 'code' => 'PACK', 'description' => 'Lakban industri, bubble wrap, kardus packing']);

        // 3. Satuan Barang (FR-11)
        $uPcs = Unit::create(['name' => 'Pieces', 'symbol' => 'Pcs']);
        $uBox = Unit::create(['name' => 'Box / Kotak', 'symbol' => 'Box']);
        $uUnit = Unit::create(['name' => 'Unit', 'symbol' => 'Unit']);
        $uSet = Unit::create(['name' => 'Set', 'symbol' => 'Set']);
        $uRoll = Unit::create(['name' => 'Roll', 'symbol' => 'Roll']);
        $uKg = Unit::create(['name' => 'Kilogram', 'symbol' => 'Kg']);

        // 4. Gudang & Lokasi Rak (FR-13)
        $whKarawang = Warehouse::create([
            'name' => 'Gudang Utama Karawang',
            'code' => 'WH-KRW',
            'address' => 'Kawasan Industri KIIC Kav. 42, Karawang Barat',
            'is_active' => true,
        ]);

        $whCikarang = Warehouse::create([
            'name' => 'Gudang Transit Cikarang',
            'code' => 'WH-CKR',
            'address' => 'Jl. Boulevard Industri Delta Silicon 3, Cikarang',
            'is_active' => true,
        ]);

        $locA1 = Location::create(['warehouse_id' => $whKarawang->id, 'code' => 'A-01', 'name' => 'Rak Elektronik Lantai 1']);
        $locA2 = Location::create(['warehouse_id' => $whKarawang->id, 'code' => 'A-02', 'name' => 'Rak Perkakas Lantai 1']);
        $locB1 = Location::create(['warehouse_id' => $whKarawang->id, 'code' => 'B-01', 'name' => 'Rak APD & Keselamatan']);
        $locB2 = Location::create(['warehouse_id' => $whKarawang->id, 'code' => 'B-02', 'name' => 'Rak Sparepart Mesin']);
        $locC1 = Location::create(['warehouse_id' => $whCikarang->id, 'code' => 'C-01', 'name' => 'Zona Packing Cikarang']);

        // 5. Supplier (FR-12)
        $supMaju = Supplier::create([
            'name' => 'PT Maju Logistik Sentosa',
            'contact_person' => 'Hendro Wijaya',
            'phone' => '021-8977651',
            'email' => 'sales@majulogistik.co.id',
            'address' => 'Jl. Daan Mogot KM 14 No. 8, Jakarta Barat',
            'is_active' => true,
        ]);

        $supBerkah = Supplier::create([
            'name' => 'CV Sumber Berkah Teknik',
            'contact_person' => 'Agus Priyanto',
            'phone' => '0812-9876-5432',
            'email' => 'info@sumberberkahteknik.com',
            'address' => 'Komplek Pertokoan Glodok Jaya No. 12, Jakarta Barat',
            'is_active' => true,
        ]);

        $supSurya = Supplier::create([
            'name' => 'PT Surya Graha Perkasa',
            'contact_person' => 'Iwan Darmawan',
            'phone' => '021-5544332',
            'email' => 'kontrak@suryagraha.co.id',
            'address' => 'Kawasan Pergudangan Pantai Indah Dadap Blok F, Tangerang',
            'is_active' => true,
        ]);

        // 6. Master Barang (FR-10)
        $itemsData = [
            [
                'sku' => 'ELK-SENS-001',
                'name' => 'Sensor Proximity Induktif M12',
                'category_id' => $catElectronics->id,
                'unit_id' => $uPcs->id,
                'warehouse_id' => $whKarawang->id,
                'location_id' => $locA1->id,
                'purchase_price' => 125000,
                'selling_price' => 175000,
                'current_stock' => 45,
                'min_stock' => 15,
                'description' => 'Sensor deteksi logam otomatis untuk conveyor belt pabrik.',
            ],
            [
                'sku' => 'ELK-KAB-002',
                'name' => 'Kabel Power NYMHY 3x1.5mm (100M)',
                'category_id' => $catElectronics->id,
                'unit_id' => $uRoll->id,
                'warehouse_id' => $whKarawang->id,
                'location_id' => $locA1->id,
                'purchase_price' => 450000,
                'selling_price' => 550000,
                'current_stock' => 12,
                'min_stock' => 10,
                'description' => 'Kabel serabut tembaga murni standar SNI.',
            ],
            [
                'sku' => 'TOL-BOR-001',
                'name' => 'Bor Baterai Cordless Impact Drill 18V',
                'category_id' => $catTools->id,
                'unit_id' => $uSet->id,
                'warehouse_id' => $whKarawang->id,
                'location_id' => $locA2->id,
                'purchase_price' => 850000,
                'selling_price' => 1150000,
                'current_stock' => 8,
                'min_stock' => 5,
                'description' => 'Set bor impact lengkap 2 baterai lithium dan charger cepat.',
            ],
            [
                'sku' => 'TOL-KUN-002',
                'name' => 'Kunci Pas Ring Set 8-24mm (14 Pcs)',
                'category_id' => $catTools->id,
                'unit_id' => $uSet->id,
                'warehouse_id' => $whKarawang->id,
                'location_id' => $locA2->id,
                'purchase_price' => 320000,
                'selling_price' => 420000,
                'current_stock' => 20,
                'min_stock' => 8,
                'description' => 'Bahan Chrome Vanadium tahan karat dan presisi tinggi.',
            ],
            [
                'sku' => 'APD-HLM-001',
                'name' => 'Helm Safety Proyek Putih V-Gard',
                'category_id' => $catSafety->id,
                'unit_id' => $uPcs->id,
                'warehouse_id' => $whKarawang->id,
                'location_id' => $locB1->id,
                'purchase_price' => 45000,
                'selling_price' => 65000,
                'current_stock' => 4, // Kritis (<= min_stock 10)
                'min_stock' => 10,
                'description' => 'Helm pelindung kepala standar ANSI Z89.1 dengan tali dagu elastis.',
            ],
            [
                'sku' => 'APD-SRG-002',
                'name' => 'Sarung Tangan Nitrile Tahan Kimia',
                'category_id' => $catSafety->id,
                'unit_id' => $uBox->id,
                'warehouse_id' => $whKarawang->id,
                'location_id' => $locB1->id,
                'purchase_price' => 95000,
                'selling_price' => 135000,
                'current_stock' => 3, // Kritis (<= min_stock 8)
                'min_stock' => 8,
                'description' => 'Isi 100 pcs per box, bebas bubuk, ukuran L.',
            ],
            [
                'sku' => 'SPR-BRG-001',
                'name' => 'Deep Groove Ball Bearing 6205-2RS',
                'category_id' => $catSparepart->id,
                'unit_id' => $uPcs->id,
                'warehouse_id' => $whKarawang->id,
                'location_id' => $locB2->id,
                'purchase_price' => 55000,
                'selling_price' => 80000,
                'current_stock' => 50,
                'min_stock' => 20,
                'description' => 'Bearing motor industri dengan seal karet ganda.',
            ],
            [
                'sku' => 'SPR-VBL-002',
                'name' => 'V-Belt Industri Tipe B-54',
                'category_id' => $catSparepart->id,
                'unit_id' => $uPcs->id,
                'warehouse_id' => $whKarawang->id,
                'location_id' => $locB2->id,
                'purchase_price' => 65000,
                'selling_price' => 90000,
                'current_stock' => 0, // Habis (Out of Stock)
                'min_stock' => 10,
                'description' => 'Sabuk transmisi daya mesin kompresor dan pompa.',
            ],
            [
                'sku' => 'PCK-LKB-001',
                'name' => 'Lakban Bening Industri 48mm x 100M',
                'category_id' => $catOffice->id,
                'unit_id' => $uRoll->id,
                'warehouse_id' => $whCikarang->id,
                'location_id' => $locC1->id,
                'purchase_price' => 14000,
                'selling_price' => 20000,
                'current_stock' => 120,
                'min_stock' => 30,
                'description' => 'Daya rekat tinggi 45 micron untuk pengemasan karton.',
            ],
        ];

        $createdItems = [];
        foreach ($itemsData as $data) {
            $createdItems[] = Item::create($data);
        }

        // 7. Data Riwayat Transaksi Barang Masuk Awal (FR-20)
        $stockIn1 = StockIn::create([
            'document_number' => 'IN-20261015-0001',
            'date' => now()->subDays(2)->toDateString(),
            'supplier_id' => $supMaju->id,
            'warehouse_id' => $whKarawang->id,
            'user_id' => $staff->id,
            'total_items' => 3,
            'total_quantity' => 110,
            'total_amount' => 15500000,
            'notes' => 'Penerimaan stok awal kuartal IV dari PT Maju Logistik Sentosa.',
            'status' => 'completed',
        ]);

        // Detail 1: Sensor
        StockInDetail::create([
            'stock_in_id' => $stockIn1->id,
            'item_id' => $createdItems[0]->id,
            'location_id' => $locA1->id,
            'quantity' => 50,
            'unit_price' => 125000,
            'subtotal' => 6250000,
        ]);
        StockMovement::create([
            'item_id' => $createdItems[0]->id,
            'warehouse_id' => $whKarawang->id,
            'user_id' => $staff->id,
            'type' => 'in',
            'quantity' => 50,
            'balance_before' => 0,
            'balance_after' => 50,
            'reference_type' => StockIn::class,
            'reference_id' => $stockIn1->id,
            'reference_number' => $stockIn1->document_number,
            'notes' => 'Penerimaan stok masuk awal',
            'created_at' => now()->subDays(2)->setHour(9)->setMinute(15),
        ]);

        // Detail 2: Bearing
        StockInDetail::create([
            'stock_in_id' => $stockIn1->id,
            'item_id' => $createdItems[6]->id,
            'location_id' => $locB2->id,
            'quantity' => 60,
            'unit_price' => 55000,
            'subtotal' => 3300000,
        ]);
        StockMovement::create([
            'item_id' => $createdItems[6]->id,
            'warehouse_id' => $whKarawang->id,
            'user_id' => $staff->id,
            'type' => 'in',
            'quantity' => 60,
            'balance_before' => 0,
            'balance_after' => 60,
            'reference_type' => StockIn::class,
            'reference_id' => $stockIn1->id,
            'reference_number' => $stockIn1->document_number,
            'notes' => 'Penerimaan stok masuk awal',
            'created_at' => now()->subDays(2)->setHour(9)->setMinute(20),
        ]);

        // 8. Transaksi Barang Keluar (FR-30)
        $stockOut1 = StockOut::create([
            'document_number' => 'OUT-20261016-0001',
            'date' => now()->subDay()->toDateString(),
            'recipient' => 'Divisi Pemeliharaan Mesin Line 2',
            'warehouse_id' => $whKarawang->id,
            'user_id' => $staff->id,
            'total_items' => 2,
            'total_quantity' => 15,
            'notes' => 'Pengeluaran darurat untuk perbaikan mesin stamping line 2.',
            'status' => 'completed',
        ]);

        StockOutDetail::create([
            'stock_out_id' => $stockOut1->id,
            'item_id' => $createdItems[0]->id,
            'quantity' => 5,
            'notes' => 'Penggantian sensor proximity line 2',
        ]);
        StockMovement::create([
            'item_id' => $createdItems[0]->id,
            'warehouse_id' => $whKarawang->id,
            'user_id' => $staff->id,
            'type' => 'out',
            'quantity' => -5,
            'balance_before' => 50,
            'balance_after' => 45,
            'reference_type' => StockOut::class,
            'reference_id' => $stockOut1->id,
            'reference_number' => $stockOut1->document_number,
            'notes' => 'Barang keluar untuk Divisi Pemeliharaan Mesin Line 2',
            'created_at' => now()->subDay()->setHour(14)->setMinute(30),
        ]);

        StockOutDetail::create([
            'stock_out_id' => $stockOut1->id,
            'item_id' => $createdItems[6]->id,
            'quantity' => 10,
            'notes' => 'Penggantian bearing motor conveyor',
        ]);
        StockMovement::create([
            'item_id' => $createdItems[6]->id,
            'warehouse_id' => $whKarawang->id,
            'user_id' => $staff->id,
            'type' => 'out',
            'quantity' => -10,
            'balance_before' => 60,
            'balance_after' => 50,
            'reference_type' => StockOut::class,
            'reference_id' => $stockOut1->id,
            'reference_number' => $stockOut1->document_number,
            'notes' => 'Barang keluar untuk Divisi Pemeliharaan Mesin Line 2',
            'created_at' => now()->subDay()->setHour(14)->setMinute(35),
        ]);

        // 9. Initial Audit Log
        AuditLog::record('system_init', 'System', null, null, ['message' => 'Sistem Inventory Gudang berhasil diinisialisasi dengan data default']);
    }
}
