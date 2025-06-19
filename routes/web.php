<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::post('login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.store');

    Route::get('register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
    Route::post('register', [App\Http\Controllers\AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::delete('cart/{id}', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('cart/item/items', [App\Http\Controllers\CartController::class, 'items'])->name('cart.items');
    Route::post('cart', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
    Route::put('cart/{id}/quantity', [App\Http\Controllers\CartController::class, 'updateQuantity'])->name('cart.update.quantity');

    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/bukti/{id}', [App\Http\Controllers\OrderController::class, 'updateBukti'])->name('orders.update.bukti');
    Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/order', [App\Http\Controllers\OrderController::class, 'order'])->name('orders.order');
    Route::get('/orders/{id}/detail', [App\Http\Controllers\OrderController::class, 'detail'])->name('orders.detail');

    Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('review.store');

    Route::get('profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [App\Http\Controllers\ProfileController::class, 'store'])->name('profile.put');
    Route::put('profile/address/{user_id}', [App\Http\Controllers\ProfileController::class, 'updateAddress'])->name('profile.address.put');

});

Route::prefix('wilayah')->name('wilayah.')->group(function () {
    // Basic wilayah endpoints
    Route::get('provinsi', [WilayahController::class, 'getProvinsi'])->name('provinsi');
    Route::get('kota/{provinsi_id}', [WilayahController::class, 'getKota'])->name('kota');
    Route::get('kecamatan/{kota_id}', [WilayahController::class, 'getKecamatan'])->name('kecamatan');
    Route::get('kelurahan/{kecamatan_id}', [WilayahController::class, 'getKelurahan'])->name('kelurahan');

    Route::get('tujuan', [App\Http\Controllers\CourirController::class, 'getDestination'])->name('tujuan');
    Route::get('ongkir', [App\Http\Controllers\CourirController::class, 'getOngkir'])->name('ongkir');
    // Advanced endpoints
    Route::get('search', [WilayahController::class, 'searchWilayah'])->name('search');
    Route::get('lengkap', [WilayahController::class, 'getWilayahLengkap'])->name('lengkap');

    // Admin/Maintenance endpoints (optional: add middleware for admin only)
    Route::delete('cache', [WilayahController::class, 'clearCache'])->name('clear-cache');
    Route::get('status', [WilayahController::class, 'getApiStatus'])->name('status');
});

Route::resource('products', App\Http\Controllers\ProductController::class)->names('products');
Route::get('/peta', [App\Http\Controllers\PetaController::class, 'index'])->name('peta.index');
