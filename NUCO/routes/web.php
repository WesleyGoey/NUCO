<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

require __DIR__ . '/auth.php';

// Home -> redirect to products listing (menu)
Route::get('/', function () {
    return redirect()->route('menu');
})->name('home');
Route::get('/products', [MenuController::class, 'index'])->name('menu');
Route::get('/products/{product}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');

Route::prefix('waiter')->name('waiter.')->middleware(['auth','verified'])->group(function () {
    Route::get('/tables', function () {
        return view('waiter.tables');
    })->name('tables');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');
    Route::get('/orders', function () {
        return view('waiter.orders');
    })->name('orders');
    Route::get('/cart', function () {
        return view('waiter.cart');
    })->name('cart');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});