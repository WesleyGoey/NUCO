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
use App\Http\Controllers\Owner\PaymentController as OwnerPaymentController;
use App\Http\Controllers\Owner\ReviewController as OwnerReviewController;
use App\Http\Controllers\Owner\TableController as OwnerTableController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // ⭐ ADD THIS LINE

require __DIR__ . '/auth.php';

Route::get('/', [MenuController::class, 'index'])->name('home');
Route::get('/products', [MenuController::class, 'index'])->name('menu');
Route::get('/products/{product}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts');

Route::get('/orders', [OrderController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('orders');

Route::get('/orders/{order}', [OrderController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('orders.show');

Route::post('/orders/{order}/processing', [OrderController::class, 'markProcessing'])
    ->middleware(['auth', 'verified'])
    ->name('orders.processing');

Route::post('/orders/{order}/ready', [OrderController::class, 'markReady'])
    ->middleware(['auth', 'verified'])
    ->name('orders.ready');

Route::post('/orders/{order}/sent', [OrderController::class, 'markSent'])
    ->middleware(['auth', 'verified'])
    ->name('orders.sent');

Route::post('/orders/{order}/completed', [OrderController::class, 'markCompleted'])
    ->middleware(['auth', 'verified'])
    ->name('orders.completed');

Route::prefix('waiter')->name('waiter.')->middleware(['auth','verified', 'role:waiter'])->group(function () {
    Route::get('/tables', [TableController::class, 'index'])->name('tables');
    Route::post('/tables/select', [TableController::class, 'select'])->name('tables.select');
    Route::post('/tables/cancel', [TableController::class, 'cancel'])->name('tables.cancel');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/cart/update-note', [CartController::class, 'updateNotes'])->name('cart.update-note');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear'); // ✅ ADDED
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

Route::prefix('reviewer')->name('reviewer.')->middleware(['auth','verified', 'role:reviewer'])->group(function () {
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/thankyou', [ReviewController::class, 'thankyou'])->name('thankyou');
});

Route::prefix('cashier')->name('cashier.')->middleware(['auth','verified', 'role:cashier'])->group(function () {
    Route::get('/checkout', [CashierController::class, 'checkout'])->name('checkout');
    Route::post('/payment/process', [CashierController::class, 'processPayment'])->name('payment.process');
    Route::post('/payment/store', [CashierController::class, 'storePayment'])->name('payment.store'); // ✅ ADDED
    Route::get('/order-history', [CashierController::class, 'orderHistory'])->name('order.history');
});

// ✅ Midtrans callback route (no auth middleware)
Route::post('/midtrans/callback', [CashierController::class, 'handleCallback'])->name('midtrans.callback');

Route::prefix('owner')->name('owner.')->middleware(['auth','verified', 'role:owner'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [OwnerUserController::class, 'index'])->name('users');
    Route::get('/users/create', [OwnerUserController::class, 'create'])->name('users.create');
    Route::post('/users', [OwnerUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [OwnerUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [OwnerUserController::class, 'update'])->name('users.update');

    // Products
    Route::get('/products', [OwnerProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [OwnerProductController::class, 'create'])->name('products.create');
    Route::post('/products', [OwnerProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [OwnerProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}', [OwnerProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [OwnerProductController::class, 'destroy'])->name('products.destroy');
    
    // Product Recipe Management
    Route::get('/products/{product}/recipe', [OwnerProductController::class, 'showRecipe'])->name('products.recipe');
    Route::post('/products/{product}/recipe', [OwnerProductController::class, 'updateRecipe'])->name('products.recipe.update');

    // Inventory
    Route::get('/inventory', [OwnerInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [OwnerInventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [OwnerInventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{ingredient}/edit', [OwnerInventoryController::class, 'edit'])->name('inventory.edit');
    Route::patch('/inventory/{ingredient}', [OwnerInventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{ingredient}', [OwnerInventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/inventory/{ingredient}/stock', [OwnerInventoryController::class, 'showStockForm'])->name('inventory.stock');
    Route::post('/inventory/{ingredient}/stock', [OwnerInventoryController::class, 'updateStock'])->name('inventory.stock.update');

    // Discounts
    Route::get('/discounts', [OwnerDiscountController::class, 'index'])->name('discounts.index');
    Route::get('/discounts/create', [OwnerDiscountController::class, 'create'])->name('discounts.create');
    Route::post('/discounts', [OwnerDiscountController::class, 'store'])->name('discounts.store');
    Route::get('/discounts/{discount}/edit', [OwnerDiscountController::class, 'edit'])->name('discounts.edit');
    Route::patch('/discounts/{discount}', [OwnerDiscountController::class, 'update'])->name('discounts.update');
    Route::delete('/discounts/{discount}', [OwnerDiscountController::class, 'destroy'])->name('discounts.destroy');

    // Tables
    Route::get('/tables', [OwnerTableController::class, 'index'])->name('tables.index');
    Route::get('/tables/create', [OwnerTableController::class, 'create'])->name('tables.create');
    Route::post('/tables', [OwnerTableController::class, 'store'])->name('tables.store');
    Route::get('/tables/{table}/edit', [OwnerTableController::class, 'edit'])->name('tables.edit');
    Route::patch('/tables/{table}', [OwnerTableController::class, 'update'])->name('tables.update');
    Route::delete('/tables/{table}', [OwnerTableController::class, 'destroy'])->name('tables.destroy');

    // Payments
    Route::get('/payments', [OwnerPaymentController::class, 'index'])->name('payments.index');

    // Reviews
    Route::get('/reviews', [OwnerReviewController::class, 'index'])->name('reviews.index');
});

Route::prefix('chef')->name('chef.')->middleware(['auth','verified', 'role:chef'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'inventory'])->name('inventory');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ⚠️ TEMPORARY DEBUG ROUTES - Remove after testing!
Route::get('/debug/db', function () {
    try {
        // Test 1: Check database connection
        DB::connection()->getPdo();
        $dbConnected = true;
        $dbError = null;
    } catch (\Exception $e) {
        $dbConnected = false;
        $dbError = $e->getMessage();
    }

    // Test 2: Count users table
    try {
        $usersCount = DB::table('users')->count();
    } catch (\Exception $e) {
        $usersCount = 'Error: ' . $e->getMessage();
    }

    // Test 3: Get database name
    try {
        $dbName = DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        $dbName = 'Error: ' . $e->getMessage();
    }

    return response()->json([
        'database_connected' => $dbConnected,
        'database_error' => $dbError,
        'database_name' => $dbName,
        'users_count' => $usersCount,
        'db_host' => config('database.connections.mysql.host'),
        'db_port' => config('database.connections.mysql.port'),
        'db_database' => config('database.connections.mysql.database'),
        'db_username' => config('database.connections.mysql.username'),
    ]);
});

Route::get('/debug/routes', function () {
    $routeCollection = Route::getRoutes();
    
    $routes = [];
    foreach ($routeCollection as $route) {
        $routes[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
        ];
    }
    
    // Take first 50 routes
    $first50Routes = array_slice($routes, 0, 50);
    
    return response()->json([
        'total_routes' => count($routes),
        'first_50_routes' => $first50Routes,
    ]);
});

Route::get('/debug/app', function () {
    return response()->json([
        'app_name' => config('app.name'),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'app_url' => config('app.url'),
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION,
        'octane_server' => config('octane.server'),
    ]);
});