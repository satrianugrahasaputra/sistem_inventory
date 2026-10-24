<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(InventoryService $inventoryService)
    {
        $summary = $inventoryService->getDashboardSummary();
        return view('dashboard.index', $summary);
    }
}
