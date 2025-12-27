<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::with('user')->orderBy('created_at','desc')->paginate(25);
        return view('owner.reviews.index', compact('reviews'));
    }
}