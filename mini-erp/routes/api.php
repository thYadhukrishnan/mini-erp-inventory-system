<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryApiController;
use App\Http\Controllers\Api\PurchaseOrderApiController;
use App\Http\Controllers\Api\ReportApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public: Login
Route::post('/login', [AuthController::class, 'login']);

// Protected: require Sanctum token
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Inventory
    Route::get('/inventory',           [InventoryApiController::class, 'index']);
    Route::get('/inventory/low-stock', [InventoryApiController::class, 'lowStock']);

    // Purchase Orders
    Route::post('/purchase-orders',                        [PurchaseOrderApiController::class, 'store']);
    Route::patch('/purchase-orders/{purchaseOrder}/status',[PurchaseOrderApiController::class, 'updateStatus']);

    // Reports
    Route::get('/reports/supplier-spend', [ReportApiController::class, 'supplierSpend']);
});
