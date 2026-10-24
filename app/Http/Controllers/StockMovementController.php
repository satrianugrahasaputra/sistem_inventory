<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['item.unit', 'warehouse', 'user'])->latest('created_at')->latest('id');

        if ($itemId = $request->input('item_id')) {
            $query->where('item_id', $itemId);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('item', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        $movements = $query->paginate(15)->withQueryString();
        $items = Item::where('is_active', true)->orderBy('name')->get();

        return view('movements.index', compact('movements', 'items'));
    }

    public function itemCard(Request $request, Item $item)
    {
        $query = $item->movements()->with(['warehouse', 'user'])->latest('created_at');

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $movements = $query->paginate(20)->withQueryString();

        return view('movements.item_card', compact('item', 'movements'));
    }
}
