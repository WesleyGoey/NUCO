<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

require __DIR__ . '/auth.php';

Route::get('/', [MenuController::class, 'index'])->name('home');
Route::get('/products', [MenuController::class, 'index'])->name('menu');
Route::get('/products/{product}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');
Route::get('/orders', [OrderController::class, 'index'])
    ->middleware(['auth','verified'])
    ->name('orders');

Route::get('/orders/{order}', [OrderController::class, 'show'])
    ->middleware(['auth','verified'])
    ->name('orders.show');

Route::get('/orders/{order}/pay', [OrderController::class, 'pay'])
    ->middleware(['auth','verified'])
    ->name('orders.pay');

Route::prefix('waiter')->name('waiter.')->middleware(['auth','verified'])->group(function () {
    Route::get('/tables', [TableController::class, 'index'])->name('tables');
    Route::post('/tables/select', [TableController::class, 'select'])->name('tables.select');
    Route::post('/tables/cancel', [TableController::class, 'cancel'])->name('tables.cancel');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/update-note', [CartController::class, 'updateNote'])->name('cart.update-note');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
});


Route::prefix('reviewer')->name('reviewer.')->middleware(['auth','verified'])->group(function () {
 Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/thankyou', [ReviewController::class, 'thankyou'])->name('thankyou');
});

Route::prefix('cashier')->name('cashier.')->middleware(['auth','verified'])->group(function () {
    Route::get('/checkout', [CashierController::class, 'checkout'])->name('checkout');
    Route::post('/payment/process', [CashierController::class, 'processPayment'])->name('payment.process');
    Route::get('/order-history', [CashierController::class, 'orderHistory'])->name('order.history');
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

// top-level orders route already exists (->name('orders'))
Route::post('/orders/{order}/sent', [OrderController::class, 'markSent'])
    ->middleware(['auth','verified'])
    ->name('orders.sent');
