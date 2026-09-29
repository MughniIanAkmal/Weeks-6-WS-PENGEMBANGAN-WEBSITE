<?php

use App\Http\Controllers\Acara17Controller;
use App\Http\Controllers\Acara18Controller;
use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quickLogin');

// Dashboard & Home (All Products + Acara 13, 17, 18 Integrated)
Route::get('/', [HomeController::class, 'index'])->name('home');

// ACARA 13: Resource Controller (CRUD Otomatis)
Route::resource('product', ProductController::class);

// ACARA 13: API Controller (JSON Response)
Route::prefix('api')->name('api.')->group(function () {
    Route::apiResource('product', ApiProductController::class);
});

// ACARA 17: Query Builder
Route::prefix('acara17')->name('acara17.')->group(function () {
    Route::get('/products', [Acara17Controller::class, 'index'])->name('products.index');
    Route::get('/products/create', [Acara17Controller::class, 'create'])->name('products.create');
    Route::post('/products', [Acara17Controller::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [Acara17Controller::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [Acara17Controller::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [Acara17Controller::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/adjust-stock', [Acara17Controller::class, 'adjustStock'])->name('products.adjustStock');
    Route::get('/demos', [Acara17Controller::class, 'demos'])->name('demos');
});

// ACARA 18: Eloquent ORM
Route::prefix('acara18')->name('acara18.')->group(function () {
    Route::get('/products', [Acara18Controller::class, 'index'])->name('products.index');
    Route::get('/products/create', [Acara18Controller::class, 'create'])->name('products.create');
    Route::post('/products', [Acara18Controller::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [Acara18Controller::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [Acara18Controller::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [Acara18Controller::class, 'destroy'])->name('products.destroy');
    Route::get('/demos', [Acara18Controller::class, 'demos'])->name('demos');
});
