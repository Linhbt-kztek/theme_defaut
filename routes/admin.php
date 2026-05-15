<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\BookingDetailController;
use App\Http\Controllers\Admin\ConfigController;

use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PricingRuleController;
use App\Http\Controllers\Admin\RevenueReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Web\Inventory\DashboardController as InventoryDashboardController;
use App\Http\Controllers\Web\Inventory\ExportReceiptController;
use App\Http\Controllers\Web\Inventory\ImportReceiptController;
use App\Http\Controllers\Web\Inventory\ProductController;
use App\Http\Controllers\Web\Inventory\StockController;
use App\Http\Controllers\Web\Inventory\StockLogController;
use App\Http\Controllers\Web\Inventory\WarehouseController;
use App\Http\Controllers\Web\RoleController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => ['auth', 'web-login']], function () {

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/', [HomeController::class, 'index'])->name('home');

   

    // quản lý tài khoản
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::post('/user/search', [UserController::class, 'index'])->name('user_search');
    Route::get('/user/create', [UserController::class, 'create'])->name('user_create');
    Route::post('/user_create', [UserController::class, 'store']);
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user_edit');
    Route::get('/user/edit_on_user/{id}', [UserController::class, 'editOnUser'])->name('user_edit_on_user');
    Route::put('/user_update/{id}', [UserController::class, 'update']);
    Route::any('/user/{id}', [UserController::class, 'delete'])->name('user_delete');
    Route::get('/user/getFormAddcompanyToUser/{id}', [UserController::class, 'getFormAddcompanyToUser'])->name('user_getFormAddcompanyToUser');
    Route::post('/user_AddcompanyToUser/updateFormAddcompanyToUser', [UserController::class, 'updateFormAddcompanyToUser'])->name('user_updateFormAddcompanyToUser');
    Route::get('/user/getFormAddTicketTypeToUser/{id}', [UserController::class, 'getFormAddTicketTypeToUser'])->name('user_getFormAddTicketTypeToUser');
    Route::post('/users/{id}/saveFormAddTicketTypeToUser', [UserController::class, 'saveFormAddTicketTypeToUser'])->name('user_saveFormAddTicketTypeToUser');

    // Quản lý nhóm quyền
    Route::any('roles', [RoleController::class, 'index'])->name('role.index');
    Route::get('roles/show-create-form', [RoleController::class, 'showCreateForm'])->name('role.show_create_form');
    Route::post('roles/create', [RoleController::class, 'create'])->name('role.create');
    Route::get('roles/show-edit-form/{id}', [RoleController::class, 'showEditForm'])->name('role.show_edit_form');
    Route::post('roles/update', [RoleController::class, 'update'])->name('role.update');
    Route::any('roles/delete/{id}', [RoleController::class, 'delete'])->name('role.delete');

    Route::get('config', [ConfigController::class, 'index'])->name('config.index');
    Route::post('config/update', [ConfigController::class, 'update'])->name('config.update');
    Route::post('config/updateInvoice', [ConfigController::class, 'updateInvoice'])->name('config.updateInvoice');
    Route::put('config/{id}', [ConfigController::class, 'processBanner'])->name('config.process-banner');

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryDashboardController::class, 'index'])->name('dashboard');
        Route::get('stock-summary', [InventoryDashboardController::class, 'stockSummary'])->name('stock-summary');

        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('warehouses', WarehouseController::class)->except(['show']);
        Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');

        Route::resource('import-receipts', ImportReceiptController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('import-receipts/{id}/complete', [ImportReceiptController::class, 'complete'])->name('import-receipts.complete');
        Route::post('import-receipts/{id}/cancel', [ImportReceiptController::class, 'cancel'])->name('import-receipts.cancel');

        Route::resource('export-receipts', ExportReceiptController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('export-receipts/{id}/complete', [ExportReceiptController::class, 'complete'])->name('export-receipts.complete');
        Route::post('export-receipts/{id}/cancel', [ExportReceiptController::class, 'cancel'])->name('export-receipts.cancel');

        Route::get('stock-logs', [StockLogController::class, 'index'])->name('stock-logs.index');
    });

});
// Route::get('rabbitMq/test', [RabbitMqController::class, 'test'])->name('rabbitMq.test');
