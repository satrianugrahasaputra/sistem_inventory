<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Halaman Aplikasi (Wajib Login)
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Barang
    Route::resource('items', ItemController::class);
    Route::post('/items/{item}/toggle-active', [ItemController::class, 'toggleActive'])->name('items.toggle-active');

    // Master Data Pendukung
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('units', UnitController::class)->except(['create', 'edit', 'show']);
    Route::resource('suppliers', SupplierController::class)->except(['create', 'edit', 'show']);
    Route::resource('warehouses', WarehouseController::class)->except(['create', 'edit', 'show']);
    Route::post('/warehouses/{warehouse}/locations', [WarehouseController::class, 'storeLocation'])->name('warehouses.locations.store');
    Route::delete('/locations/{location}', [WarehouseController::class, 'destroyLocation'])->name('warehouses.locations.destroy');

    // Transaksi Barang Masuk
    Route::resource('stock-ins', StockInController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/stock-ins/{stock_in}/void', [StockInController::class, 'void'])->name('stock-ins.void');

    // Transaksi Barang Keluar
    Route::resource('stock-outs', StockOutController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/stock-outs/{stock_out}/void', [StockOutController::class, 'void'])->name('stock-outs.void');

    // Kartu Stok & Riwayat Mutasi
    Route::get('/movements', [StockMovementController::class, 'index'])->name('movements.index');
    Route::get('/movements/item/{item}', [StockMovementController::class, 'itemCard'])->name('movements.item');

    // Laporan
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/movements', [ReportController::class, 'movements'])->name('reports.movements');

    // Stock Opname
    Route::resource('stock-opnames', StockOpnameController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/stock-opnames/{stock_opname}/approve', [StockOpnameController::class, 'approve'])
        ->name('stock-opnames.approve')
        ->middleware('role:admin,manager');

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});
