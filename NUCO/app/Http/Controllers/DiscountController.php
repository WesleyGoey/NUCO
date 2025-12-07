<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\View\View;

class DiscountController extends Controller
{
    /**
     * Show list of discounts and whether each is active (by period).
     */
    public function index(): View
    {
        $today = now()->toDateString();

        // load only periods that are currently valid
        $discounts = Discount::with(['periods' => function ($q) use ($today) {
            $q->whereDate('start_date', '<=', $today)
              ->where(function ($q2) use ($today) {
                  $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
              });
        }])->get()
          ->map(function ($d) {
              $d->is_active = $d->periods->isNotEmpty();
              return $d;
          });

        return view('discounts', compact('discounts'));
    }
}