<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
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
            'users' => User::whereIn('role', ['waiter','chef','cashier'])->count(),
            'products' => Product::count(),
            'ingredients' => Ingredient::count(),
            'tables' => RestaurantTable::count(),
            'discounts' => Discount::count(),
            'orders' => Order::count(),
            'payments' => Payment::count(),
            'reviews' => Review::count(),
        ];

        return view('owner.dashboard', compact('counts'));
    }
}