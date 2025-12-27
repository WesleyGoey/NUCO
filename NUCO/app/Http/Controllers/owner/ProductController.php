<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::orderBy('id','asc')->paginate(30);
        return view('owner.products.index', compact('products'));
    }
}