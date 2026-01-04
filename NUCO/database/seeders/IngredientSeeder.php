<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use Faker\Factory as Faker;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $units = ['kg', 'g', 'ltr', 'pcs'];

        for ($i = 1; $i <= 12; $i++) {
            Ingredient::create([
                'name' => ucfirst($faker->word()) . " Ingredient",
                'unit' => $faker->randomElement($units),
                'current_stock' => $faker->randomFloat(2, 5, 200),
                'min_stock' => $faker->randomFloat(2, 1, 10),
            ]);
        }
    }
}