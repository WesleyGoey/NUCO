<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\waiter\TableController;
use App\Http\Controllers\waiter\CartController;
use App\Http\Controllers\reviewer\ReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Cashier\CashierController;
use App\Http\Controllers\Chef\InventoryController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\UserController as OwnerUserController;
use App\Http\Controllers\Owner\ProductController as OwnerProductController;
use App\Http\Controllers\Owner\InventoryController as OwnerInventoryController;
use App\Http\Controllers\Owner\DiscountController as OwnerDiscountController;
use App\Http\Controllers\Owner\OrderController as OwnerOrderController;
use App\Http\Controllers\Owner\PaymentController as OwnerPaymentController;
use App\Http\Controllers\Owner\ReviewController as OwnerReviewController;
use App\Http\Controllers\Owner\TableController as OwnerTableController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Public / Guest
|--------------------------------------------------------------------------
*/
Route::get('/', [MenuController::class, 'index'])->name('home');
Route::get('/products', [MenuController::class, 'index'])->name('menu');
Route::get('/products/{product}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');

/*
|--------------------------------------------------------------------------
| Orders (shared)
|--------------------------------------------------------------------------
*/
Route::get('/orders', [OrderController::class, 'index'])
    ->middleware(['auth','verified'])
    ->name('orders');

Route::get('/orders/{order}', [OrderController::class, 'show'])
    ->middleware(['auth','verified'])
    ->name('orders.show');

Route::post('/orders/{order}/sent', [OrderController::class, 'markSent'])
    ->middleware(['auth','verified'])
    ->name('orders.sent');

Route::post('/orders/{order}/processing', [OrderController::class, 'markProcessing'])
    ->middleware(['auth','verified'])
    ->name('orders.processing');

Route::post('/orders/{order}/ready', [OrderController::class, 'markReady'])
    ->middleware(['auth','verified'])
    ->name('orders.ready');

/*
|--------------------------------------------------------------------------
| Waiter
|--------------------------------------------------------------------------
*/
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
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

/*
|--------------------------------------------------------------------------
| Reviewer
|--------------------------------------------------------------------------
*/
Route::prefix('reviewer')->name('reviewer.')->middleware(['auth','verified'])->group(function () {
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/thankyou', [ReviewController::class, 'thankyou'])->name('thankyou');
});

/*
|--------------------------------------------------------------------------
| Cashier
|--------------------------------------------------------------------------
*/
Route::prefix('cashier')->name('cashier.')->middleware(['auth','verified'])->group(function () {
    Route::get('/checkout', [CashierController::class, 'checkout'])->name('checkout');
    Route::post('/payment/process', [CashierController::class, 'processPayment'])->name('payment.process');
    Route::get('/order-history', [CashierController::class, 'orderHistory'])->name('order.history');
});

/*
|--------------------------------------------------------------------------
| Owner (namespaced)
|--------------------------------------------------------------------------
*/
Route::prefix('owner')->name('owner.')->middleware(['auth','verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/users', [OwnerUserController::class, 'index'])->name('users');
    Route::get('/users/create', [OwnerUserController::class, 'create'])->name('users.create');
    Route::post('/users', [OwnerUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [OwnerUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [OwnerUserController::class, 'update'])->name('users.update');
    
    Route::resource('products', OwnerProductController::class);
    
    // Reviews (read-only)
    Route::get('/reviews', [OwnerReviewController::class, 'index'])->name('reviews.index');
});

/*
|--------------------------------------------------------------------------
| Chef
|--------------------------------------------------------------------------
*/
Route::prefix('chef')->name('chef.')->middleware(['auth','verified'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'inventory'])->name('inventory');
});

/*
|--------------------------------------------------------------------------
| Authenticated profile routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});