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
            // users first (other tables reference users)
            UserSeeder::class,
            CategorySeeder::class,

            // basic reference data
            RestaurantTableSeeder::class,
            ProductSeeder::class,
            IngredientSeeder::class,

            // discounts + periods
            DiscountSeeder::class,
            // orders depend on users, products, tables, discounts
            OrderSeeder::class,

            // payments after orders
            PaymentSeeder::class,

            // inventory logs after ingredients & orders
            InventoryLogSeeder::class,

            // reviews (reviewers expected in users)
            ReviewSeeder::class,
        ]);
    }
}
