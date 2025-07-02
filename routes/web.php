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
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\DashboardController; // Add DashboardController
Use App\Http\Controllers\BillController; // Add BillController
use App\Http\Controllers\ContactController; // Add ContactController
use App\Http\Controllers\NoteController; // Add NoteController
use App\Http\Controllers\LandedCostController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\TaskController; // Add TaskController
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\QuotationStatusController; // Add QuotationStatusController


// In routes/web.php
Route::get('/phpinfo', function () {
    phpinfo();
});


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
    Route::resource('user-roles', UserRoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('customers', CustomerController::class); // Already protected internally
    Route::resource('contacts', ContactController::class);
    Route::resource('leads', LeadController::class);
    Route::post('leads/{lead}/activities', [LeadController::class, 'storeActivity'])->name('leads.activities.store');
    Route::resource('products', ProductController::class);
    Route::resource('product-features', ProductFeatureController::class);
    Route::resource('product-categories', ProductCategoryController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::get('customers/{customer}/contacts', [OpportunityController::class, 'getContactsByCustomer'])->name('customers.contacts'); // New API route
    Route::get('opportunities/kanban', [OpportunityController::class, 'kanban'])->name('opportunities.kanban');
    Route::patch('opportunities/{opportunity}/stage', [OpportunityController::class, 'updateStage'])->name('opportunities.updateStage');
    Route::resource('opportunities', OpportunityController::class);
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{quotation}/items', [QuotationController::class, 'getItemsJson'])->name('quotations.items.json');
    Route::post('quotations/{quotation}/send-email', [QuotationController::class, 'sendEmail'])->name('quotations.sendEmail');
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/items', [InvoiceController::class, 'getOrderItemsJson'])->name('orders.items.json');
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
    Route::resource('addresses', AddressController::class);
    Route::put('quotations/{quotation}/status', [QuotationStatusController::class, 'update'])->name('quotations.status.update');
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send-reminder', [InvoiceController::class, 'sendReminder'])->name('invoices.sendReminder');
    Route::get('invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'printPdf'])->name('invoices.pdf');
    Route::resource('journal-entries', JournalEntryController::class); // Allow full CRUD for manual entries
    Route::post('purchase-orders/{purchase_order}/payments', [PaymentController::class, 'store'])->name('purchase-orders.payments.store');
    Route::get('purchase-orders/{purchase_order}/print', [PurchaseOrderController::class, 'printPdf'])->name('purchase-orders.print');

    Route::post('bills/{bill}/payments', [PaymentController::class, 'store'])->name('bills.payments.store');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::resource('payments', PaymentController::class)->except(['create', 'edit']);
    
    Route::resource('bills', BillController::class);
    Route::post('bills/{bill}/restore', [BillController::class, 'restore'])->name('bills.restore');
    Route::delete('bills/{bill}/force-delete', [BillController::class, 'forceDelete'])->name('bills.force-delete');
    
    Route::post('leads/{lead}/convert', [LeadController::class, 'convertToCustomer'])->name('leads.convertToCustomer');

    Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Landed Costs
    Route::post('purchase-orders/{purchase_order}/landed-costs', [LandedCostController::class, 'store'])->name('landed-costs.store');
    Route::delete('landed-costs/{landed_cost}', [LandedCostController::class, 'destroy'])->name('landed-costs.destroy');
    Route::post('purchase-orders/{purchase_order}/landed-costs/apportion', [LandedCostController::class, 'apportion'])->name('landed-costs.apportion');

    // Goods Receipts
    Route::get('purchase-orders/{purchase_order}/goods-receipts/create', [GoodsReceiptController::class, 'create'])->name('goods-receipts.create');
    Route::post('purchase-orders/{purchase_order}/goods-receipts', [GoodsReceiptController::class, 'store'])->name('goods-receipts.store');
    Route::get('goods-receipts/{goods_receipt}', [GoodsReceiptController::class, 'show'])->name('goods-receipts.show');
  
    // Reports
    Route::get('reports/sales', [ReportController::class, 'salesByPeriod'])->name('reports.sales');
    Route::get('reports/sales-by-product', [ReportController::class, 'salesByProduct'])->name('reports.sales-by-product');
    Route::get('reports/sales-by-customer', [ReportController::class, 'salesByCustomer'])->name('reports.sales-by-customer');
    Route::get('reports/sales-by-category', [ReportController::class, 'salesByCategory'])->name('reports.sales-by-category');
    Route::get('reports/sales-by-employee', [ReportController::class, 'salesByEmployee'])->name('reports.sales-by-employee');
});
Route::middleware('auth')->group(function () {
    Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('settings', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
