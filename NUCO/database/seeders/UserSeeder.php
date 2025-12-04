<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'Owner',
            'email' => 'owner@gmail.com',
            'password' => bcrypt('owner123'),
            'phone' => '12345678',
            'role' => 'owner',
            'status' => 'active'
        ]);
        User::create([
            'username' => 'Waiter',
            'email' => 'waiter@gmail.com',
            'password' => bcrypt('waiter123'),
            'phone' => '12345678',
            'role' => 'waiter',
                'status' => 'active'
        ]);
        User::create([
            'username' => 'Chef',
            'email' => 'chef@gmail.com',
            'password' => bcrypt('chef123'),
            'phone' => '12345678',
            'role' => 'chef',
                'status' => 'active'
        ]);
        User::create([
            'username' => 'Cashier',
            'email' => 'cashier@gmail.com',
            'password' => bcrypt('cashier123'),
            'phone' => '12345678',
           'role' => 'cashier',
                'status' => 'active'
        ]);
        User::create([
            'username' => 'Reviewer',
            'email' => 'reviewer@gmail.com',
            'password' => bcrypt('reviewer123'),
            'phone' => '12345678',
            'role' => 'reviewer',
            'status' => 'active'
        ]);
    }
}
