<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;
    protected Warehouse $warehouse;
    protected Item $item;
    protected InventoryService $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = new InventoryService();

        $this->user = User::factory()->create(['role' => 'staff']);
        $cat = Category::create(['name' => 'Test Cat', 'code' => 'TC']);
        $unit = Unit::create(['name' => 'Pcs', 'symbol' => 'Pcs']);
        $this->supplier = Supplier::create(['name' => 'Test Supplier']);
        $this->warehouse = Warehouse::create(['name' => 'Main Warehouse', 'code' => 'WH-M']);

        $this->item = Item::create([
            'sku' => 'TEST-001',
            'name' => 'Barang Uji Coba',
            'category_id' => $cat->id,
            'unit_id' => $unit->id,
            'warehouse_id' => $this->warehouse->id,
            'purchase_price' => 10000,
            'current_stock' => 10,
            'min_stock' => 5,
        ]);
    }

    public function test_stock_in_increases_item_stock_and_creates_movement_atomically(): void
    {
        $stockIn = $this->inventoryService->recordStockIn(
            [
                'supplier_id' => $this->supplier->id,
                'warehouse_id' => $this->warehouse->id,
                'date' => now()->toDateString(),
                'notes' => 'Uji barang masuk',
            ],
            [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 15,
                    'unit_price' => 12000,
                ],
            ],
            $this->user
        );

        $this->item->refresh();
        $this->assertEquals(25, $this->item->current_stock);
        $this->assertEquals('completed', $stockIn->status);
        $this->assertEquals(15, $stockIn->total_quantity);

        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $this->item->id,
            'type' => 'in',
            'quantity' => 15,
            'balance_before' => 10,
            'balance_after' => 25,
            'reference_number' => $stockIn->document_number,
        ]);
    }

    public function test_stock_out_decreases_item_stock_and_creates_movement_atomically(): void
    {
        $stockOut = $this->inventoryService->recordStockOut(
            [
                'recipient' => 'Divisi Uji Coba',
                'warehouse_id' => $this->warehouse->id,
                'date' => now()->toDateString(),
            ],
            [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 4,
                ],
            ],
            $this->user
        );

        $this->item->refresh();
        $this->assertEquals(6, $this->item->current_stock);
        $this->assertEquals('completed', $stockOut->status);

        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $this->item->id,
            'type' => 'out',
            'quantity' => -4,
            'balance_before' => 10,
            'balance_after' => 6,
            'reference_number' => $stockOut->document_number,
        ]);
    }

    public function test_stock_out_never_allows_negative_stock(): void
    {
        $this->expectException(ValidationException::class);

        // Current stock is 10, request 11
        $this->inventoryService->recordStockOut(
            [
                'recipient' => 'Toko Cabang',
                'date' => now()->toDateString(),
            ],
            [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 11,
                ],
            ],
            $this->user
        );

        $this->item->refresh();
        $this->assertEquals(10, $this->item->current_stock); // Must remain untouched
    }

    public function test_void_stock_in_reverts_stock_safely(): void
    {
        $stockIn = $this->inventoryService->recordStockIn(
            ['supplier_id' => $this->supplier->id, 'date' => now()->toDateString()],
            [['item_id' => $this->item->id, 'quantity' => 10, 'unit_price' => 10000]],
            $this->user
        );

        $this->item->refresh();
        $this->assertEquals(20, $this->item->current_stock);

        $this->inventoryService->voidStockIn($stockIn, 'Salah input nota', $this->user);

        $this->item->refresh();
        $this->assertEquals(10, $this->item->current_stock);
        $this->assertEquals('cancelled', $stockIn->fresh()->status);
    }

    public function test_void_stock_out_reverts_stock_safely(): void
    {
        $stockOut = $this->inventoryService->recordStockOut(
            ['recipient' => 'Divisi Lapangan', 'date' => now()->toDateString()],
            [['item_id' => $this->item->id, 'quantity' => 5]],
            $this->user
        );

        $this->item->refresh();
        $this->assertEquals(5, $this->item->current_stock);

        $this->inventoryService->voidStockOut($stockOut, 'Pengeluaran dibatalkan pengawas', $this->user);

        $this->item->refresh();
        $this->assertEquals(10, $this->item->current_stock);
        $this->assertEquals('cancelled', $stockOut->fresh()->status);
    }
}
