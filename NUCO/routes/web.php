<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

require __DIR__ . '/auth.php';

Route::get('/', [MenuController::class, 'index'])->name('home');
Route::get('/products', [MenuController::class, 'index'])->name('menu');
Route::get('/products/{product}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');

Route::prefix('waiter')->name('waiter.')->middleware(['auth','verified'])->group(function () {
    Route::get('/tables', [TableController::class, 'index'])->name('tables');
    Route::post('/tables/select', [TableController::class, 'select'])->name('tables.select');

    // menu-cart route for waiter flow (cart-enabled)
    Route::get('/cart', [MenuController::class, 'cart'])->name('cart');

    // cart add (session-based) for waiter flow
    // Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');
    Route::get('/orders', function () {
        return view('waiter.orders');
    })->name('orders');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});