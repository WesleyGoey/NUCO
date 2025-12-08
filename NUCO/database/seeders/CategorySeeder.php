<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Main Course', 'Snacks', 'Drinks', 'Dessert'];

        foreach ($names as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}