<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

// Phân hệ 1: Website Bán Sách Trực Tuyến dành cho Khách hàng (Online Storefront)
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [StorefrontController::class, 'index'])->name('index');
    Route::get('/books/{book}', [StorefrontController::class, 'show'])->name('book');
    Route::get('/cart', [StorefrontController::class, 'cart'])->name('cart');
    Route::post('/cart/add/{book}', [StorefrontController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [StorefrontController::class, 'updateCart'])->name('cart.update');
    Route::get('/lookup-customer', [StorefrontController::class, 'lookupCustomer'])->name('lookup-customer');
    Route::post('/checkout', [StorefrontController::class, 'checkout'])->name('checkout');
    Route::get('/track', [StorefrontController::class, 'trackOrder'])->name('track');
});

// Phân hệ 2: Hệ thống Quản trị Nhà sách & Bán hàng tại quầy (Admin ERP & Smart POS)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/quick-search', [DashboardController::class, 'quickSearch'])->name('api.quick-search');

Route::post('/books/{book}/restock', [BookController::class, 'restock'])->name('books.restock');
Route::resource('books', BookController::class);

Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('authors', AuthorController::class)->except(['show']);
Route::resource('customers', CustomerController::class);

Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show']);
