<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discount;
use App\Models\Period;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        // percent discount
        $d1 = Discount::create([
            'name' => 'Weekend Sale',
            'value' => 10,
            'type' => 'percent',
            'min_order_amount' => 20000,
        ]);

        Period::create([
            'discount_id' => $d1->id,
            'start_date' => Carbon::now()->subDays(3)->toDateString(),
            'end_date' => Carbon::now()->addDays(10)->toDateString(),
            'description' => 'Weekend promo',
        ]);

        // flat amount discount
        $d2 = Discount::create([
            'name' => 'Flat IDR 5000 Off',
            'value' => 5000,
            'type' => 'amount',
            'min_order_amount' => 50000,
        ]);

        Period::create([
            'discount_id' => $d2->id,
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
            'description' => 'Limited time flat discount',
        ]);
    }
}