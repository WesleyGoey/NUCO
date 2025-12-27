<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Discount;

class DiscountController extends Controller
{
    public function index(): View
    {
        $discounts = Discount::with('periods')->orderBy('id','desc')->paginate(25);
        return view('owner.discounts.index', compact('discounts'));
    }
}