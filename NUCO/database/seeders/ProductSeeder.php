<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Ambil semua kategori
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            return;
        }

        // Buat 5 produk per kategori
        foreach ($categories as $category) {
            for ($i = 0; $i < 5; $i++) {
                Product::create([
                    'name' => $faker->words(3, true),
                    'category_id' => $category->id,
                    'price' => $faker->numberBetween(10000, 100000),
                    'description' => $faker->sentence(),
                    'image_path' => 'default.jpg', // ✅ FIXED: Gunakan 'image_path'
                    'is_available' => $faker->boolean(80), // ✅ ADDED: 80% chance available
                ]);
            }
        }
    }
}