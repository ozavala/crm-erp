<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrmUserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFeatureController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\DashboardController; // Add DashboardController


Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return app(DashboardController::class)->index(); // Use the new controller
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('crm-users', CrmUserController::class);
    Route::resource('user-roles',UserRoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('leads', LeadController::class);
    Route::post('leads/{lead}/activities', [LeadController::class, 'storeActivity'])->name('leads.activities.store');
    Route::resource('products', ProductController::class);
    Route::resource('product-features', ProductFeatureController::class);
    Route::resource('product-categories', ProductCategoryController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('opportunities', OpportunityController::class);
    Route::resource('quotations', QuotationController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('leads/{lead}/convert', [LeadController::class, 'convertToCustomer'])->name('leads.convertToCustomer');
});

require __DIR__.'/auth.php';
