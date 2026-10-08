<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\DocumentController;
use App\Controllers\HomeController;
use App\Controllers\PublicController;
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

Route::group([AuthMiddleware::class], function(){
    Route::get('/admin',[AdminController::class,'index']);
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

    Route::post('/auth/logout', [AuthController::class, 'logout']);
});