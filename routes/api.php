<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\InvoiceApiController;
use App\Http\Controllers\Api\PurchaseOrderApiController;
use App\Http\Controllers\Api\ReportingApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Customers API
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerApiController::class, 'index']);
        Route::get('/{customer}', [CustomerApiController::class, 'show']);
        Route::post('/', [CustomerApiController::class, 'store']);
        Route::put('/{customer}', [CustomerApiController::class, 'update']);
        Route::delete('/{customer}', [CustomerApiController::class, 'destroy']);
    });

    // Products API
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductApiController::class, 'index']);
        Route::get('/{product}', [ProductApiController::class, 'show']);
        Route::post('/', [ProductApiController::class, 'store']);
        Route::put('/{product}', [ProductApiController::class, 'update']);
        Route::delete('/{product}', [ProductApiController::class, 'destroy']);
        Route::get('/{product}/stock', [ProductApiController::class, 'getStock']);
        Route::post('/{product}/stock', [ProductApiController::class, 'updateStock']);
    });

    // Orders API
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderApiController::class, 'index']);
        Route::get('/{order}', [OrderApiController::class, 'show']);
        Route::post('/', [OrderApiController::class, 'store']);
        Route::put('/{order}', [OrderApiController::class, 'update']);
        Route::delete('/{order}', [OrderApiController::class, 'destroy']);
        Route::post('/{order}/status', [OrderApiController::class, 'updateStatus']);
    });

    // Invoices API
    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceApiController::class, 'index']);
        Route::get('/{invoice}', [InvoiceApiController::class, 'show']);
        Route::post('/', [InvoiceApiController::class, 'store']);
        Route::put('/{invoice}', [InvoiceApiController::class, 'update']);
        Route::delete('/{invoice}', [InvoiceApiController::class, 'destroy']);
        Route::post('/{invoice}/send', [InvoiceApiController::class, 'sendInvoice']);
    });

    // Purchase Orders API
    Route::prefix('purchase-orders')->group(function () {
        Route::get('/', [PurchaseOrderApiController::class, 'index']);
        Route::get('/{purchaseOrder}', [PurchaseOrderApiController::class, 'show']);
        Route::post('/', [PurchaseOrderApiController::class, 'store']);
        Route::put('/{purchaseOrder}', [PurchaseOrderApiController::class, 'update']);
        Route::delete('/{purchaseOrder}', [PurchaseOrderApiController::class, 'destroy']);
        Route::post('/{purchaseOrder}/status', [PurchaseOrderApiController::class, 'updateStatus']);
        Route::post('/{purchaseOrder}/receive', [PurchaseOrderApiController::class, 'receiveStock']);
    });

    // Reporting API
    Route::prefix('reports')->group(function () {
        Route::get('/sales', [ReportingApiController::class, 'salesReport']);
        Route::get('/inventory', [ReportingApiController::class, 'inventoryReport']);
        Route::get('/cash-flow', [ReportingApiController::class, 'cashFlowReport']);
        Route::get('/profitability', [ReportingApiController::class, 'profitabilityReport']);
        Route::get('/supplier-performance', [ReportingApiController::class, 'supplierPerformanceReport']);
    });

    // Dashboard API
    Route::get('/dashboard/stats', function () {
        return response()->json([
            'total_customers' => \App\Models\Customer::count(),
            'total_products' => \App\Models\Product::count(),
            'pending_orders' => \App\Models\Order::where('status', 'pending')->count(),
            'pending_invoices' => \App\Models\Invoice::where('status', 'sent')->count(),
            'low_stock_products' => \App\Models\Product::whereHas('warehouses', function ($query) {
                $query->whereRaw('quantity <= reorder_point');
            })->count(),
            'monthly_sales' => \App\Models\Order::whereMonth('created_at', now()->month)->sum('total_amount'),
        ]);
    });
});

// Public endpoints (no authentication required)
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
});

Route::get('/version', function () {
    return response()->json([
        'version' => '1.0.0',
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION
    ]);
}); 