<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOut;
use App\Models\StockOutDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Generate unique sequential document number: PREFIX-YYYYMMDD-0001
     */
    public function generateDocumentNumber(string $prefix): string
    {
        $today = now()->format('Ymd');
        $base = "{$prefix}-{$today}-";

        if ($prefix === 'IN') {
            $last = StockIn::where('document_number', 'like', "{$base}%")->latest('id')->value('document_number');
        } elseif ($prefix === 'OUT') {
            $last = StockOut::where('document_number', 'like', "{$base}%")->latest('id')->value('document_number');
        } elseif ($prefix === 'OPN') {
            $last = StockOpname::where('document_number', 'like', "{$base}%")->latest('id')->value('document_number');
        } else {
            $last = null;
        }

        if ($last) {
            $parts = explode('-', $last);
            $seq = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }

        return $base . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Catat Transaksi Barang Masuk secara Atomik
     */
    public function recordStockIn(array $headerData, array $itemsData, User $user): StockIn
    {
        return DB::transaction(function () use ($headerData, $itemsData, $user) {
            $docNumber = $headerData['document_number'] ?? $this->generateDocumentNumber('IN');

            $stockIn = StockIn::create([
                'document_number' => $docNumber,
                'date' => $headerData['date'] ?? now()->toDateString(),
                'supplier_id' => $headerData['supplier_id'],
                'warehouse_id' => $headerData['warehouse_id'] ?? null,
                'user_id' => $user->id,
                'notes' => $headerData['notes'] ?? null,
                'status' => 'completed',
            ]);

            $totalItems = 0;
            $totalQty = 0;
            $totalAmount = 0;

            foreach ($itemsData as $entry) {
                $qty = intval($entry['quantity'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $unitPrice = floatval($entry['unit_price'] ?? 0);
                $subtotal = $qty * $unitPrice;

                // Lock row item untuk atomisitas & cegah race condition
                $item = Item::where('id', $entry['item_id'])->lockForUpdate()->firstOrFail();

                $balanceBefore = $item->current_stock;
                $balanceAfter = $balanceBefore + $qty;

                // Update stok item
                $item->current_stock = $balanceAfter;
                if ($unitPrice > 0) {
                    $item->purchase_price = $unitPrice;
                }
                $item->save();

                // Simpan detail
                StockInDetail::create([
                    'stock_in_id' => $stockIn->id,
                    'item_id' => $item->id,
                    'location_id' => $entry['location_id'] ?? $item->location_id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'notes' => $entry['notes'] ?? null,
                ]);

                // Simpan riwayat mutasi kartu stok
                StockMovement::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $stockIn->warehouse_id ?? $item->warehouse_id,
                    'user_id' => $user->id,
                    'type' => 'in',
                    'quantity' => $qty,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => StockIn::class,
                    'reference_id' => $stockIn->id,
                    'reference_number' => $stockIn->document_number,
                    'notes' => "Barang Masuk dari Supplier. Doc: {$stockIn->document_number}",
                    'created_at' => now(),
                ]);

                $totalItems++;
                $totalQty += $qty;
                $totalAmount += $subtotal;
            }

            if ($totalItems === 0) {
                throw ValidationException::withMessages([
                    'items' => 'Minimal harus menambahkan satu barang dengan jumlah lebih dari 0.',
                ]);
            }

            $stockIn->update([
                'total_items' => $totalItems,
                'total_quantity' => $totalQty,
                'total_amount' => $totalAmount,
            ]);

            AuditLog::record(
                'create_stock_in',
                'StockIn',
                $stockIn->id,
                null,
                ['doc' => $stockIn->document_number, 'items' => $totalItems, 'qty' => $totalQty]
            );

            return $stockIn;
        });
    }

    /**
     * Catat Transaksi Barang Keluar secara Atomik (Dijamin Stok Tidak Pernah Negatif)
     */
    public function recordStockOut(array $headerData, array $itemsData, User $user): StockOut
    {
        return DB::transaction(function () use ($headerData, $itemsData, $user) {
            $docNumber = $headerData['document_number'] ?? $this->generateDocumentNumber('OUT');

            $stockOut = StockOut::create([
                'document_number' => $docNumber,
                'date' => $headerData['date'] ?? now()->toDateString(),
                'recipient' => $headerData['recipient'],
                'warehouse_id' => $headerData['warehouse_id'] ?? null,
                'user_id' => $user->id,
                'notes' => $headerData['notes'] ?? null,
                'status' => 'completed',
            ]);

            $totalItems = 0;
            $totalQty = 0;

            foreach ($itemsData as $entry) {
                $qty = intval($entry['quantity'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                // Lock row item untuk atomisitas & cegah race condition
                $item = Item::where('id', $entry['item_id'])->lockForUpdate()->firstOrFail();

                // VALIDASI ATURAN BISNIS #1: Stok tidak boleh minus
                if ($item->current_stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok tidak mencukupi untuk barang '{$item->name}' (SKU: {$item->sku}). Stok tersedia: {$item->current_stock}, jumlah diminta: {$qty}.",
                    ]);
                }

                $balanceBefore = $item->current_stock;
                $balanceAfter = $balanceBefore - $qty;

                // Update stok item
                $item->current_stock = $balanceAfter;
                $item->save();

                // Simpan detail
                StockOutDetail::create([
                    'stock_out_id' => $stockOut->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'notes' => $entry['notes'] ?? null,
                ]);

                // Simpan mutasi kartu stok
                StockMovement::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $stockOut->warehouse_id ?? $item->warehouse_id,
                    'user_id' => $user->id,
                    'type' => 'out',
                    'quantity' => -$qty,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => StockOut::class,
                    'reference_id' => $stockOut->id,
                    'reference_number' => $stockOut->document_number,
                    'notes' => "Barang Keluar ke: {$stockOut->recipient}. Doc: {$stockOut->document_number}",
                    'created_at' => now(),
                ]);

                $totalItems++;
                $totalQty += $qty;
            }

            if ($totalItems === 0) {
                throw ValidationException::withMessages([
                    'items' => 'Minimal harus menambahkan satu barang dengan jumlah lebih dari 0.',
                ]);
            }

            $stockOut->update([
                'total_items' => $totalItems,
                'total_quantity' => $totalQty,
            ]);

            AuditLog::record(
                'create_stock_out',
                'StockOut',
                $stockOut->id,
                null,
                ['doc' => $stockOut->document_number, 'items' => $totalItems, 'qty' => $totalQty]
            );

            return $stockOut;
        });
    }

    /**
     * Batalkan (Void) Transaksi Barang Masuk
     */
    public function voidStockIn(StockIn $stockIn, string $reason, User $user): void
    {
        DB::transaction(function () use ($stockIn, $reason, $user) {
            if ($stockIn->isCancelled()) {
                throw ValidationException::withMessages(['status' => 'Transaksi ini sudah dibatalkan sebelumnya.']);
            }

            $stockIn->load('details.item');

            // Cek apakah stok cukup jika dikurangi kembali
            foreach ($stockIn->details as $detail) {
                $item = Item::where('id', $detail->item_id)->lockForUpdate()->firstOrFail();
                if ($item->current_stock < $detail->quantity) {
                    throw ValidationException::withMessages([
                        'reason' => "Tidak dapat membatalkan transaksi: Stok barang '{$item->name}' saat ini ({$item->current_stock}) lebih sedikit dari jumlah masuk ({$detail->quantity}). Sebagian barang mungkin sudah terpakai di transaksi keluar.",
                    ]);
                }
            }

            // Kembalikan stok
            foreach ($stockIn->details as $detail) {
                $item = Item::where('id', $detail->item_id)->lockForUpdate()->firstOrFail();
                $balanceBefore = $item->current_stock;
                $balanceAfter = $balanceBefore - $detail->quantity;

                $item->current_stock = $balanceAfter;
                $item->save();

                StockMovement::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $stockIn->warehouse_id ?? $item->warehouse_id,
                    'user_id' => $user->id,
                    'type' => 'void_in',
                    'quantity' => -$detail->quantity,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => StockIn::class,
                    'reference_id' => $stockIn->id,
                    'reference_number' => $stockIn->document_number,
                    'notes' => "PEMBATALAN Barang Masuk ({$stockIn->document_number}): {$reason}",
                    'created_at' => now(),
                ]);
            }

            $stockIn->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
            ]);

            AuditLog::record(
                'void_stock_in',
                'StockIn',
                $stockIn->id,
                ['status' => 'completed'],
                ['status' => 'cancelled', 'reason' => $reason]
            );
        });
    }

    /**
     * Batalkan (Void) Transaksi Barang Keluar
     */
    public function voidStockOut(StockOut $stockOut, string $reason, User $user): void
    {
        DB::transaction(function () use ($stockOut, $reason, $user) {
            if ($stockOut->isCancelled()) {
                throw ValidationException::withMessages(['status' => 'Transaksi ini sudah dibatalkan sebelumnya.']);
            }

            $stockOut->load('details.item');

            foreach ($stockOut->details as $detail) {
                $item = Item::where('id', $detail->item_id)->lockForUpdate()->firstOrFail();
                $balanceBefore = $item->current_stock;
                $balanceAfter = $balanceBefore + $detail->quantity;

                $item->current_stock = $balanceAfter;
                $item->save();

                StockMovement::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $stockOut->warehouse_id ?? $item->warehouse_id,
                    'user_id' => $user->id,
                    'type' => 'void_out',
                    'quantity' => $detail->quantity,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => StockOut::class,
                    'reference_id' => $stockOut->id,
                    'reference_number' => $stockOut->document_number,
                    'notes' => "PEMBATALAN Barang Keluar ({$stockOut->document_number}): {$reason}",
                    'created_at' => now(),
                ]);
            }

            $stockOut->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
            ]);

            AuditLog::record(
                'void_stock_out',
                'StockOut',
                $stockOut->id,
                ['status' => 'completed'],
                ['status' => 'cancelled', 'reason' => $reason]
            );
        });
    }

    /**
     * Setujui Sesi Stock Opname dan Sesuaikan Stok
     */
    public function approveStockOpname(StockOpname $opname, User $user): void
    {
        DB::transaction(function () use ($opname, $user) {
            if (!$opname->isDraft()) {
                throw ValidationException::withMessages(['status' => 'Stock opname ini sudah diproses sebelumnya.']);
            }

            $opname->load('details.item');

            foreach ($opname->details as $detail) {
                $item = Item::where('id', $detail->item_id)->lockForUpdate()->firstOrFail();
                $diff = $detail->physical_stock - $item->current_stock;

                if ($diff !== 0) {
                    $balanceBefore = $item->current_stock;
                    $balanceAfter = $detail->physical_stock;

                    $item->current_stock = $balanceAfter;
                    $item->save();

                    StockMovement::create([
                        'item_id' => $item->id,
                        'warehouse_id' => $opname->warehouse_id ?? $item->warehouse_id,
                        'user_id' => $user->id,
                        'type' => 'adjustment',
                        'quantity' => $diff,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'reference_type' => StockOpname::class,
                        'reference_id' => $opname->id,
                        'reference_number' => $opname->document_number,
                        'notes' => "Penyesuaian Fisik Stock Opname ({$opname->document_number}). Selisih: {$diff}",
                        'created_at' => now(),
                    ]);
                }
            }

            $opname->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            AuditLog::record('approve_opname', 'StockOpname', $opname->id, ['status' => 'draft'], ['status' => 'approved']);
        });
    }

    /**
     * Ringkasan Dashboard
     */
    public function getDashboardSummary(): array
    {
        $totalItems = Item::where('is_active', true)->count();
        $totalStock = (int) Item::where('is_active', true)->sum('current_stock');
        $lowStockCount = Item::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->where('current_stock', '>', 0)
            ->count();
        $outOfStockCount = Item::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->count();

        $todayStockIn = StockIn::where('status', 'completed')
            ->whereDate('date', now()->toDateString())
            ->sum('total_quantity');

        $todayStockOut = StockOut::where('status', 'completed')
            ->whereDate('date', now()->toDateString())
            ->sum('total_quantity');

        $recentTransactions = StockMovement::with(['item.unit', 'user'])
            ->latest('created_at')
            ->take(10)
            ->get();

        $criticalItems = Item::with(['category', 'unit'])
            ->where('is_active', true)
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->orderBy('current_stock', 'asc')
            ->take(6)
            ->get();

        return [
            'total_items' => $totalItems,
            'total_stock' => $totalStock,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'critical_total' => $lowStockCount + $outOfStockCount,
            'today_stock_in' => (int) $todayStockIn,
            'today_stock_out' => (int) $todayStockOut,
            'recent_transactions' => $recentTransactions,
            'critical_items' => $criticalItems,
        ];
    }
}
