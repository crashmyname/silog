<?php

use App\Controllers\ActualDeliveryController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\CustomerController;
use App\Controllers\DocumentController;
use App\Controllers\DriverController;
use App\Controllers\HomeController;
use App\Controllers\ItemController;
use App\Controllers\PublicController;
use App\Controllers\RouteController;
use App\Controllers\SystemMenuController;
use App\Controllers\UserController;
use App\Controllers\VehicleController;
use App\Controllers\VendorController;
use Bpjs\Framework\Helpers\AuthMiddleware;
use Bpjs\Framework\Helpers\Route;
use Bpjs\Framework\Helpers\View;

Route::get('/',[HomeController::class,'index'])->name('home.index');
Route::get('/login',fn() => view('auth/login'));
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/categories',              [PublicController::class, 'categories']);
Route::get('/documents',               [PublicController::class, 'documents']);
Route::get('/stats',                   [PublicController::class, 'stats']);
Route::get('/documents/{id}/download', [PublicController::class, 'download']);
Route::get('/file/secure', function () {
    serve_secure_file();
});

Route::get('/delivery/board-data', [PublicController::class, 'deliveryBoard']);
Route::get('/system-menus', [PublicController::class, 'systemMenus']);
Route::group([AuthMiddleware::class], function(){
    Route::get('/admin',[AdminController::class,'index']);

    // User management (admin only)
    Route::get ('/admin/users/list',     [UserController::class, 'list']);
    Route::post('/admin/users',          [UserController::class, 'store']);
    Route::put ('/admin/users/{id}',     [UserController::class, 'update']);
    Route::delete('/admin/users/{id}',   [UserController::class, 'destroy']);

    // Category
    Route::get('/admin/categories',[CategoryController::class,'index'])->name('category.index');
    Route::get('/admin/categories/list', [CategoryController::class, 'list'])->name('category.list');
    Route::post('/admin/categories',[CategoryController::class,'store'])->name('category.create');
    Route::put('/admin/{id}/categories',[CategoryController::class,'update'])->name('category.update');
    Route::delete('/admin/{id}/categories',[CategoryController::class,'destroy'])->name('category.delete');

    Route::get('/admin/documents',[DocumentController::class,'index'])->name('document.index');
    Route::get('/admin/documents/list',[DocumentController::class, 'list'])    ->name('document.list');
    Route::post('/admin/documents',[DocumentController::class,'store'])->name('document.create');
    Route::put('/admin/{id}/documents',[DocumentController::class,'update'])->name('document.update');
    Route::delete('/admin/{id}/documents',[DocumentController::class,'destroy'])->name('document.delete');
    Route::get('/admin/{id}/documents/download',[DocumentController::class,'download'])->name('document.download');

    /* ============================================================
   MASTER CUSTOMER
   ============================================================ */
    Route::get   ('/admin/customers',          [CustomerController::class, 'index']);
    Route::get   ('/admin/customers/list',     [CustomerController::class, 'list']);
    Route::get   ('/admin/customers/{id}',     [CustomerController::class, 'show']);
    Route::post  ('/admin/customers',          [CustomerController::class, 'store']);
    Route::put   ('/admin/{id}/customers',     [CustomerController::class, 'update']);
    Route::delete('/admin/{id}/customers',     [CustomerController::class, 'destroy']);

    /* ============================================================
    MASTER ITEM
    ============================================================ */
    Route::get   ('/admin/items',              [ItemController::class, 'index']);
    Route::get   ('/admin/items/list',         [ItemController::class, 'list']);
    Route::post  ('/admin/items',              [ItemController::class, 'store']);
    Route::put   ('/admin/{id}/items',         [ItemController::class, 'update']);
    Route::delete('/admin/{id}/items',         [ItemController::class, 'destroy']);
    Route::post('/admin/items/import',            [ItemController::class, 'import']);
    Route::get ('/admin/items/import-template',   [ItemController::class, 'importTemplate']);

    /* ============================================================
    MASTER VEHICLE
    ============================================================ */
    Route::get   ('/admin/vehicles',           [VehicleController::class, 'index']);
    Route::get   ('/admin/vehicles/list',      [VehicleController::class, 'list']);
    Route::post  ('/admin/vehicles',           [VehicleController::class, 'store']);
    Route::put   ('/admin/{id}/vehicles',      [VehicleController::class, 'update']);
    Route::delete('/admin/{id}/vehicles',      [VehicleController::class, 'destroy']);
    Route::post('/admin/vehicles/import',          [VehicleController::class, 'import']);          // ← baru
    Route::get ('/admin/vehicles/import-template', [VehicleController::class, 'importTemplate']);

    /* ============================================================
    ACTUAL DELIVERY (transaksi)
    ============================================================ */
    Route::get   ('/admin/actual-delivery',         [ActualDeliveryController::class, 'index']);
    Route::get   ('/admin/actual-delivery/list',    [ActualDeliveryController::class, 'list']);
    Route::post  ('/admin/actual-delivery',         [ActualDeliveryController::class, 'store']);
    Route::put   ('/admin/{id}/actual-delivery',    [ActualDeliveryController::class, 'update']);
    Route::delete('/admin/{id}/actual-delivery',    [ActualDeliveryController::class, 'destroy']);
    Route::get('/admin/actual-delivery/board',      [ActualDeliveryController::class, 'board']);
    Route::get('/admin/actual-delivery/board-data', [ActualDeliveryController::class, 'boardData']);
    Route::post('/admin/actual-delivery/import', [ActualDeliveryController::class, 'import']);
    Route::post('/admin/actual-delivery/preview-import', [ActualDeliveryController::class, 'previewImport']);

    // System Menu
    Route::get   ('/admin/system-menus/list', [SystemMenuController::class, 'list']);
    Route::post  ('/admin/system-menus',      [SystemMenuController::class, 'store']);
    Route::put   ('/admin/system-menus/{id}', [SystemMenuController::class, 'update']);
    Route::delete('/admin/system-menus/{id}', [SystemMenuController::class, 'destroy']);

    // Vendor
    Route::get   ('/admin/vendors/list', [VendorController::class, 'list']);
    Route::post  ('/admin/vendors',      [VendorController::class, 'store']);
    Route::put   ('/admin/vendors/{id}', [VendorController::class, 'update']);
    Route::delete('/admin/vendors/{id}', [VendorController::class, 'destroy']);

    // Driver
    Route::get   ('/admin/drivers/list', [DriverController::class, 'list']);
    Route::post  ('/admin/drivers',      [DriverController::class, 'store']);
    Route::put   ('/admin/drivers/{id}', [DriverController::class, 'update']);
    Route::delete('/admin/drivers/{id}', [DriverController::class, 'destroy']);

    // Rute
    Route::get   ('/admin/routes/list', [RouteController::class, 'list']);
    Route::post  ('/admin/routes',      [RouteController::class, 'store']);
    Route::put   ('/admin/routes/{id}', [RouteController::class, 'update']);
    Route::delete('/admin/routes/{id}', [RouteController::class, 'destroy']);

    Route::post('/auth/logout', [AuthController::class, 'logout']);
});