<?php
// filepath: database/seeders/PaymentSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $orders = Order::where('status', 'completed')->get();
        $cashiers = User::where('role', 'cashier')->get();

        foreach ($orders as $order) {
            // skip if payment already exists
            if ($order->payment()->exists()) {
                continue;
            }

            Payment::create([
                'order_id' => $order->id,
                'user_id' => $cashiers->isNotEmpty() ? $cashiers->random()->id : null,
                'amount' => $order->total_price ?? 0,
                'method' => $faker->randomElement(['cash', 'card', 'qris']),
                'is_available' => true,
                'payment_time' => Carbon::now()->subMinutes(rand(0, 1000)),
            ]);
        }
    }
}