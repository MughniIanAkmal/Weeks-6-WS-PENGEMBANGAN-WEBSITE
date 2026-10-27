<?php

use App\Http\Controllers\Acara17Controller;
use App\Http\Controllers\Acara18Controller;
use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Project POS Toko
|--------------------------------------------------------------------------
*/

// ==========================================
// Authentication Routes (Acara 22 & Acara 23)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quickLogin');

// Profil Pengguna (Acara 23: Retrieving Authenticated User)
Route::get('/profile', [AuthController::class, 'showProfile'])->middleware('auth')->name('profile');

// Email Verification (Acara 22)
Route::get('/email/verify', [AuthController::class, 'verifyNotice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/resend', [AuthController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

// ==========================================
// Dashboard Utama POS Toko
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ==========================================
// Rute Pengelolaan Produk (Acara 21 Middleware & Acara 24 Authorization)
// ==========================================
// Pengelolaan Produk (Resource Controller dengan Otorisasi Gate & Policy)
Route::resource('product', ProductController::class);

// ==========================================
// Fitur Kasir POS: Keranjang, Checkout, dan Cetak Struk (Role: Kasir & Admin)
// ==========================================
Route::middleware(['auth', 'cek.role:kasir,admin'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', [CashierController::class, 'index'])->name('index');
    Route::post('/cart/add/{id}', [CashierController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update/{id}', [CashierController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CashierController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/clear', [CashierController::class, 'clearCart'])->name('cart.clear');
    Route::post('/cart/hold', [CashierController::class, 'holdTransaction'])->name('cart.hold');
    Route::get('/cart/resume/{holdId}', [CashierController::class, 'resumeTransaction'])->name('cart.resume');
    Route::post('/checkout', [CashierController::class, 'checkout'])->name('checkout');
    Route::get('/receipt/{id}', [CashierController::class, 'receipt'])->name('receipt');
    Route::get('/history', [CashierController::class, 'history'])->name('history');
});

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
