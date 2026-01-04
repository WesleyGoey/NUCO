<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::with('user')->orderBy('id','asc')->paginate(20);
        
        $avgRating = Review::avg('rating') ?? 0;
        $rating5 = Review::where('rating', 5)->count();
        $rating4 = Review::where('rating', 4)->count();
        $rating3 = Review::where('rating', 3)->count();
        $rating2 = Review::where('rating', 2)->count();
        $rating1 = Review::where('rating', 1)->count();
        
        $stats = [
            'avgRating' => $avgRating,
            'rating5' => $rating5,
            'rating4' => $rating4,
            'rating3' => $rating3,
            'rating2' => $rating2,
            'rating1' => $rating1,
        ];
        
        return view('owner.reviews.index', compact('reviews', 'stats'));
    }
}