<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\RestaurantTable;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index(): View
    {
        try {
            // ✅ SAFE: Add try-catch untuk setiap query
            $counts = [
                'users' => $this->safeCount(User::where('status', 'active')),
                'products' => $this->safeCount(Product::query()),
                'ingredients' => $this->safeCount(Ingredient::query()),
                'tables' => $this->safeCount(RestaurantTable::query()),
                'discounts' => $this->safeCount(Discount::query()),
                'orders' => $this->safeCount(Order::query()),
                'payments' => $this->safeCount(Payment::query()),
                'reviews' => $this->safeCount(Review::query()),
            ];

            $cards = [
                ['title'=>'Users','route'=>'owner.users','key'=>'users','icon'=>'bi-people','desc'=>'Manage staff accounts'],
                ['title'=>'Products & Recipes','route'=>'owner.products.index','key'=>'products','icon'=>'bi-card-list','desc'=>'Menu, prices & recipes'],
                ['title'=>'Inventory & Ingredients','route'=>'owner.inventory.index','key'=>'ingredients','icon'=>'bi-box-seam','desc'=>'Manage stock & ingredients'],
                ['title'=>'Tables','route'=>'owner.tables.index','key'=>'tables','icon'=>'bi-table','desc'=>'Manage dining layout'],
                ['title'=>'Discounts','route'=>'owner.discounts.index','key'=>'discounts','icon'=>'bi-tag','desc'=>'Promos & periods'],
                ['title'=>'Orders','route'=>'orders','key'=>'orders','icon'=>'bi-basket','desc'=>'Realtime order monitor'],
                ['title'=>'Payments','route'=>'owner.payments.index','key'=>'payments','icon'=>'bi-credit-card','desc'=>'Payment methods & records'],
                ['title'=>'Reviews','route'=>'owner.reviews.index','key'=>'reviews','icon'=>'bi-chat-left-text','desc'=>'Customer feedback'],
            ];

            // ✅ SAFE: Add try-catch untuk route check
            foreach ($cards as &$c) {
                try {
                    $c['href'] = Route::has($c['route']) ? route($c['route']) : null;
                } catch (\Exception $e) {
                    Log::warning("Route check failed for {$c['route']}: " . $e->getMessage());
                    $c['href'] = null;
                }
            }
            unset($c);

            return view('owner.dashboard', compact('counts', 'cards'));
            
        } catch (\Exception $e) {
            // ✅ LOG ERROR DETAILS
            Log::error('Dashboard error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // ✅ FALLBACK: Show simple error message
            return view('owner.dashboard', [
                'counts' => [
                    'users' => 0,
                    'products' => 0,
                    'ingredients' => 0,
                    'tables' => 0,
                    'discounts' => 0,
                    'orders' => 0,
                    'payments' => 0,
                    'reviews' => 0,
                ],
                'cards' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Safe count with error handling
     */
    private function safeCount($query): int
    {
        try {
            return $query->count();
        } catch (\Exception $e) {
            Log::error('Count query failed: ' . $e->getMessage());
            return 0;
        }
    }
}