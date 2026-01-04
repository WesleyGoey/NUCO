<?php
// filepath: database/seeders/PaymentSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker; // ✅ TAMBAHKAN

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // ✅ GANTI fake() dengan ini

        $orders = Order::where('status', 'completed')->get();
        $cashiers = User::where('role', 'cashier')->get();

        foreach ($orders as $order) {
            // skip if payment already exists
            if ($order->payment()->exists()) {
                continue;
            }

            // ✅ Generate unique transaction ID
            $transactionId = 'ORDER-' . $order->id . '-' . $faker->unique()->numerify('######');

            Payment::create([
                'order_id' => $order->id,
                'user_id' => $cashiers->isNotEmpty() ? $cashiers->random()->id : null,
                'amount' => $order->total_price ?? 0,
                'transaction_id' => $transactionId,
                'snap_token' => null,
                'status' => 'success',
                'payment_time' => Carbon::now()->subMinutes(rand(0, 1000)),
            ]);
        }
    }
}