<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();
        
        // Ambil semua kategori
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            return;
        }

        // Buat 10 produk dengan gambar placeholder
        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => ucfirst($faker->words(2, true)),
                'category_id' => $categories->random()->id,
                'description' => $faker->sentence(10),
                'price' => $faker->numberBetween(10000, 100000),
                'is_available' => $faker->boolean(85),
                'image_path' => null, // Akan ditampilkan sebagai placeholder
            ]);
        }
    }
}