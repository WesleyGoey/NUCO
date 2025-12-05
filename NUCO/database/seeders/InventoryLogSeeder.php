<?php
// filepath: database/seeders/InventoryLogSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryLog;
use App\Models\Ingredient;
use App\Models\User;

class InventoryLogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $ingredients = Ingredient::all();
        $users = User::all();

        if ($ingredients->isEmpty() || $users->isEmpty()) {
            return;
        }

        // create 50 random inventory logs (purchase/consumption/waste)
        for ($i = 0; $i < 50; $i++) {
            $ingredient = $ingredients->random();
            $user = $users->random();

            $type = $faker->randomElement(['purchase', 'consumption', 'waste']);
            $amount = $type === 'purchase' ? $faker->randomFloat(2, 1, 50) : -1 * $faker->randomFloat(2, 0.1, 10);

            InventoryLog::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => $user->id,
                'change_amount' => $amount,
                'type' => $type,
            ]);
        }
    }
}