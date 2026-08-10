<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes here are automatically prefixed with /api and assigned
| the 'api' middleware group by bootstrap/app.php.
|
*/

Route::middleware('auth:sanctum')->group(function () {

    // ─── Dashboard ──────────────────────────────────────────────────────────
    Route::get('dashboard', [DashboardController::class, 'index']);

    // ─── Orders (CRUD) ──────────────────────────────────────────────────────
    Route::apiResource('orders', OrderController::class);

    // ─── Tracking History ───────────────────────────────────────────────────
    Route::get('orders/{order}/tracking', [TrackingController::class, 'index']);
    Route::post('orders/{order}/tracking', [TrackingController::class, 'store']);

    // ─── Invoice / Payment ──────────────────────────────────────────────────
    Route::get('orders/{order}/invoice', [InvoiceController::class, 'show']);
    Route::patch('orders/{order}/invoice/payment', [InvoiceController::class, 'updatePayment']);

    // ─── Reports ────────────────────────────────────────────────────────────
    Route::get('reports', [ReportController::class, 'index']);
});
