<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Display guest menu / product listing with search.
     */
    public function index(Request $request): View
    {
        // categories: distinct category + total count (no availability filter)
        $categories = Product::selectRaw('category, count(*) as products_count')
            ->groupBy('category')
            ->get()
            ->map(function ($c) {
                return (object) [
                    'id' => $c->category,
                    'name' => $c->category,
                    'products_count' => $c->products_count,
                ];
            });

        $totalProductsCount = Product::count();

        $search = $request->query('search');

        // build base query (apply category filter if present)
        $productsQuery = Product::query();

        if ($request->filled('category')) {
            $productsQuery->where('category', $request->query('category'));
        }

        if (!empty($search)) {
            // search only by product name, scoped to selected category if any
            $products = $productsQuery->where('name', 'like', "%{$search}%")
                ->orderBy('id', 'asc')
                ->paginate(3)
                ->withQueryString();
        } else {
            // return Eloquent models collection for listing (no pagination)
            $products = $productsQuery->orderBy('id', 'asc')->get();
        }

        return view('guest.menu', compact('products', 'categories', 'totalProductsCount', 'search'));
    }
}