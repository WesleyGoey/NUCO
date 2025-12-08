<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // categories must match the enum values defined in the migration
        $names = ['Main Course', 'Snacks', 'Drinks', 'Dessert'];

        foreach ($names as $name) {
            DB::table('categories')->updateOrInsert(
                ['name' => $name],
                ['name' => $name]
            );
        }
    }
}