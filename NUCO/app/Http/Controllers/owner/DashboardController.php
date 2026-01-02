<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
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
        $counts = [
            'users' => User::where('status', 'active')->count(),
            'products' => Product::count(),
            'ingredients' => Ingredient::count(),
            'tables' => RestaurantTable::count(),
            'discounts' => Discount::count(),
            'orders' => Order::count(),
            'payments' => Payment::count(),
            'reviews' => Review::count(),
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

        foreach ($cards as &$c) {
            $c['href'] = Route::has($c['route']) ? route($c['route']) : null;
        }
        unset($c);

        return view('owner.dashboard', compact('counts', 'cards'));
    }
}