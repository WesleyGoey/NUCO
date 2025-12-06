<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        // create 10 products
        for ($i = 1; $i <= 10; $i++) {
            Product::create([
                'name' => ucfirst($faker->words(2, true)),
                'category' => $faker->randomElement(['Food', 'Drink', 'Dessert']),
                'description' => $faker->sentence(10),
                'price' => $faker->numberBetween(10000, 100000),
                'is_available' => $faker->boolean(85),
                'image_path' => null,
            ]);
        }
    }
}