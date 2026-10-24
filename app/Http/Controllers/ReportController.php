<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function stock(Request $request)
    {
        $query = Item::with(['category', 'unit', 'warehouse', 'location'])->where('is_active', true);

        if ($catId = $request->input('category_id')) {
            $query->where('category_id', $catId);
        }
        if ($whId = $request->input('warehouse_id')) {
            $query->where('warehouse_id', $whId);
        }
        if ($status = $request->input('status')) {
            if ($status === 'out') {
                $query->where('current_stock', '<=', 0);
            } elseif ($status === 'low') {
                $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0);
            } elseif ($status === 'normal') {
                $query->whereColumn('current_stock', '>', 'min_stock');
            }
        }

        if ($request->input('export') === 'csv') {
            return $this->exportStockCsv($query->get());
        }

        $items = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        $totalValuation = Item::where('is_active', true)->selectRaw('SUM(current_stock * purchase_price) as total_val')->value('total_val') ?? 0;

        return view('reports.stock', compact('items', 'categories', 'warehouses', 'totalValuation'));
    }

    public function movements(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $query = StockMovement::with(['item.unit', 'item.category', 'warehouse', 'user'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($itemId = $request->input('item_id')) {
            $query->where('item_id', $itemId);
        }
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($request->input('export') === 'csv') {
            return $this->exportMovementsCsv($query->get(), $dateFrom, $dateTo);
        }

        $movements = $query->latest('created_at')->paginate(20)->withQueryString();
        $items = Item::where('is_active', true)->orderBy('name')->get();

        $totalIn = (clone $query)->whereIn('type', ['in', 'void_out'])->sum('quantity');
        $totalOut = abs((clone $query)->whereIn('type', ['out', 'void_in'])->sum('quantity'));

        return view('reports.movements', compact('movements', 'items', 'dateFrom', 'dateTo', 'totalIn', 'totalOut'));
    }

    protected function exportStockCsv($items): StreamedResponse
    {
        $filename = 'laporan-stok-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['SKU', 'Nama Barang', 'Kategori', 'Gudang', 'Lokasi Rak', 'Stok Saat Ini', 'Stok Min', 'Satuan', 'Harga Beli (Rp)', 'Total Nilai (Rp)', 'Status']);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->sku,
                    $item->name,
                    $item->category->name ?? '-',
                    $item->warehouse->name ?? '-',
                    $item->location->code ?? '-',
                    $item->current_stock,
                    $item->min_stock,
                    $item->unit->symbol ?? '-',
                    $item->purchase_price,
                    $item->current_stock * $item->purchase_price,
                    $item->stock_status === 'out_of_stock' ? 'Habis' : ($item->stock_status === 'low_stock' ? 'Kritis' : 'Aman'),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function exportMovementsCsv($movements, $dateFrom, $dateTo): StreamedResponse
    {
        $filename = "laporan-mutasi-{$dateFrom}-sd-{$dateTo}.csv";

        return response()->streamDownload(function () use ($movements) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'No Referensi', 'Tipe Mutasi', 'SKU', 'Nama Barang', 'Jumlah', 'Saldo Sebelum', 'Saldo Sesudah', 'Gudang', 'Pencatat', 'Keterangan']);

            foreach ($movements as $m) {
                fputcsv($handle, [
                    $m->created_at->format('Y-m-d H:i:s'),
                    $m->reference_number ?? '-',
                    $m->type_label,
                    $m->item->sku ?? '-',
                    $m->item->name ?? '-',
                    $m->quantity,
                    $m->balance_before,
                    $m->balance_after,
                    $m->warehouse->name ?? '-',
                    $m->user->name ?? '-',
                    $m->notes ?? '-',
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
