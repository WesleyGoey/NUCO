<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // run seeders in dependency-safe order
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,

            RestaurantTableSeeder::class,
            ProductSeeder::class,
            IngredientSeeder::class,

            DiscountSeeder::class,
            OrderSeeder::class,

            PaymentSeeder::class,

            InventoryLogSeeder::class,

            ReviewSeeder::class,
        ]);
    }
}
