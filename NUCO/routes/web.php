<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DiscountController;


require __DIR__ . '/auth.php';
Route::get('/', function () {
    return view('/guest/menu');
})->name('home');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');

Route::get('/products', [MenuController::class, 'index'])->name('menu');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::view('/', 'home')->name('home');
// Route::get('/products', [ProductController::class, 'index'])->name('products');
// Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');