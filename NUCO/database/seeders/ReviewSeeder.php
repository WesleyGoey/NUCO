<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // only users with role 'reviewer' can create reviews
        $reviewers = User::where('role', 'reviewer')->get();
        if ($reviewers->isEmpty()) {
            return;
        }

        // create multiple reviews per reviewer (development only)
        foreach ($reviewers as $user) {
            // random number of reviews per reviewer (1..10)
            $count = rand(1, 10);
            for ($i = 0; $i < $count; $i++) {
                Review::create([
                    'user_id' => $user->id,
                    'rating' => $faker->numberBetween(1, 5),
                    'comment' => $faker->optional()->sentence(),
                ]);
            }
        }
    }
}