<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\View\View;

class DiscountController extends Controller
{
    /**
     * ✅ Show only ACTIVE discounts (filtered by period)
     */
    public function index(): View
    {
        $today = now()->toDateString();

        // ✅ Load only periods that are currently valid (active)
        $discounts = Discount::with(['periods' => function ($q) use ($today) {
            $q->whereDate('start_date', '<=', $today)
              ->where(function ($q2) use ($today) {
                  $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
              });
        }])
        // ✅ FILTER: Only show discounts that have active periods
        ->whereHas('periods', function ($q) use ($today) {
            $q->whereDate('start_date', '<=', $today)
              ->where(function ($q2) use ($today) {
                  $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
              });
        })
        ->orderBy('name', 'asc')
        ->paginate(20); // ✅ CHANGED to 20

        return view('discounts', compact('discounts'));
    }
}