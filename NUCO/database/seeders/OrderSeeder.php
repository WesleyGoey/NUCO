<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\RestaurantTable;
use App\Models\Payment;
use App\Models\InventoryLog;
use App\Models\Discount;
use App\Models\Review;
use App\Models\Ingredient;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $products = Product::all();
        $users = User::all();
        $tables = RestaurantTable::all();
        $discounts = Discount::all();

        if ($products->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $user = $users->isNotEmpty() ? $users->random() : null;
            $table = $tables->isNotEmpty() ? $tables->random() : null;
            $selected = $products->random(rand(1, 4));

            $order = Order::create([
                'user_id' => $user?->id,
                'restaurant_table_id' => $table?->id,
                'order_name' => $faker->sentence(2),
                'total_price' => 0,
                'status' => $faker->randomElement(['pending','processing','ready','sent','completed']),
                'discount_id' => $discounts->isNotEmpty() && $faker->boolean(20) ? $discounts->random()->id : null,
            ]);

            $total = 0;
            foreach ($selected as $prod) {
                $qty = rand(1,3);
                $subtotal = $prod->price * $qty;
                $order->products()->attach($prod->id, [
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                    'note' => $faker->optional()->sentence(3),
                ]);
                $total += $subtotal;
            }

            if ($order->discount_id) {
                $d = $discounts->first(fn($x) => $x->id === $order->discount_id);
                if ($d) {
                    if ($d->type === 'percent') {
                        $total = (int) max(0, $total - round($total * ($d->value / 100)));
                    } else {
                        $total = max(0, $total - $d->value);
                    }
                }
            }

            $order->update(['total_price' => $total]);

            if ($order->status === 'completed') {
                $transactionId = 'ORDER-' . $order->id . '-' . $faker->unique()->numerify('######');
                
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $user?->id,
                    'amount' => $total,
                    'transaction_id' => $transactionId,
                    'snap_token' => null,
                    'status' => 'success',
                    'payment_time' => Carbon::now()->subMinutes(rand(0, 720)),
                ]);
            }

            foreach ($order->products as $piv) {
                InventoryLog::create([
                    'ingredient_id' => Ingredient::inRandomOrder()->first()->id ?? null,
                    'user_id' => $user?->id,
                    'change_amount' => -1 * rand(1,5),
                    'type' => 'consumption',
                ]);
            }
        }

        foreach ($users as $u) {
            if (rand(0,1)) {
                Review::create([
                    'user_id' => $u->id,
                    'rating' => rand(3,5),
                    'comment' => $faker->sentence(),
                ]);
            }
        }

        $ingredients = Ingredient::all();
        foreach ($products as $prod) {
            if ($prod->ingredients()->count() === 0 && $ingredients->isNotEmpty()) {
                $take = $ingredients->random(min(3, $ingredients->count()));
                foreach ($take as $ing) {
                    $prod->ingredients()->attach($ing->id, [
                        'amount_needed' => rand(1, 5) / 1.0,
                    ]);
                }
            }
        }
    }
}