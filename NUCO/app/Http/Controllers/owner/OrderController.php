<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['user','table'])->orderBy('created_at','desc')->paginate(30);
        return view('owner.orders.index', compact('orders'));
    }
}