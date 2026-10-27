<?php

use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Aplikasi POS Toko Terpadu
|--------------------------------------------------------------------------
| Seluruh modul aplikasi dilebur menjadi satu kesatuan sistem bisnis POS:
| 1. Dashboard & Katalog Produk
| 2. Manajemen Produk (CRUD + Upload & Resize Gambar)
| 3. Mesin Kasir POS, Transaksi, & Pengiriman Struk Email
| 4. Laporan & Ekspor PDF (DomPDF)
| 5. Manajemen Berkas & Media (File Storage)
| 6. Autentikasi & Profil Pengguna
*/

// ==========================================
// 1. Dashboard Utama & Katalog Publik
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ==========================================
// 2. Autentikasi & Manajemen Pengguna
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quickLogin');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');

    // Verifikasi Email
    Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerification'])->middleware('throttle:6,1')->name('verification.resend');
});

// ==========================================
// 3. Manajemen Produk Terintegrasi (/products)
// ==========================================
Route::resource('products', ProductController::class)->names([
    'index'   => 'product.index',
    'create'  => 'product.create',
    'store'   => 'product.store',
    'show'    => 'product.show',
    'edit'    => 'product.edit',
    'update'  => 'product.update',
    'destroy' => 'product.destroy',
]);
// Alias redirect kompatibilitas rute tunggal 'product' ke 'products'
Route::get('/product', fn() => redirect()->route('product.index'));

// ==========================================
// 4. Mesin Kasir POS & Transaksi (/cashier) - Role: Kasir & Admin
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
    Route::post('/receipt/{id}/send-email', [CashierController::class, 'sendReceiptEmail'])->name('receipt.email');
    Route::get('/history', [CashierController::class, 'history'])->name('history');
});

// ==========================================
// 5. Cetak Laporan PDF (/reports) - Integrasi DomPDF
// ==========================================
Route::middleware(['auth', 'cek.role:kasir,admin'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/products/pdf', [LaporanController::class, 'exportProdukPdf'])->name('products.pdf');
    Route::get('/transactions/pdf', [LaporanController::class, 'exportTransaksiPdf'])->name('transactions.pdf');
    Route::get('/receipt/{id}/pdf', [LaporanController::class, 'exportSingleTransactionPdf'])->name('receipt.pdf');
});

// ==========================================
// 6. Manajemen Berkas & Media (/media) - Integrasi File Storage
// ==========================================
Route::middleware(['auth', 'cek.role:admin'])->prefix('media')->name('media.')->group(function () {
    Route::get('/', [UploadController::class, 'index'])->name('index');
    Route::post('/upload', [UploadController::class, 'store'])->name('store');
    Route::get('/download/{filename}', [UploadController::class, 'download'])->name('download');
    Route::delete('/{filename}', [UploadController::class, 'destroy'])->name('destroy');
});

// ==========================================
// 7. RESTful API Produk (/api/product)
// ==========================================
Route::prefix('api')->name('api.')->group(function () {
    Route::apiResource('product', ApiProductController::class);
});
