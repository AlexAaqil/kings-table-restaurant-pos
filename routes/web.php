<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Products\ProductCategoryController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\WorkShiftController;
use App\Http\Controllers\Payments\KCBMpesaExpressController;

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::view('/contact', 'contact')->name('contact-page');
Route::post('/contact', [MessageController::class, 'store'])->name('messages.store');

Route::middleware(['auth', 'verified', 'active'])->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/work-shift/start', [WorkShiftController::class, 'start'])->name('work-shift.start');
    Route::post('/work-shift/end', [WorkShiftController::class, 'end'])->name('work-shift.end');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('shop', [ProductController::class, 'shop'])->name('shop');
    Route::post('/sales/store', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
    Route::patch('/sales/{sale}/edit', [SaleController::class, 'update'])->name('sales.update');
    Route::get('/sales/cashier', [Salecontroller::class, 'cashierSales'])->name('cashier.sales');
    Route::get('/receipt/{sale}', [SaleController::class, 'receipt'])->name('sales.receipt');

    Route::middleware(['admin'])->group(function() {
        Route::resource('users', UserController::class)->except('show');

        Route::resource('messages', MessageController::class)->only('index', 'edit', 'destroy');

        Route::resource('product-categories', ProductCategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except('show');
        Route::get('/products/images/delete/{id}', [ProductController::class, 'deleteProductImage'])->name('products.delete_image');
        Route::post('/products/images/sort', [ProductController::class, 'sortProductImages'])->name('products.sort_images');

        Route::resource('sales', SaleController::class)->except('create', 'store', 'edit', 'update', 'show');
    });
});

require __DIR__.'/auth.php';

Route::get('/pay', [KCBMpesaExpressController::class, 'showForm']);
Route::post('/pay', [KCBMpesaExpressController::class, 'initiatePayment']);
