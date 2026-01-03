<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $selectedCategory = $request->query('category', '');

        $hasCategoryId = Schema::hasColumn('products', 'category_id');
        $totalProductsCount = Product::count();

        if ($hasCategoryId) {
            // Load semua kategori (untuk tombol filter)
            $allCategories = Category::orderBy('id')->get();

            // Map kategori dengan produk yang sesuai filter
            $categories = $allCategories->map(function ($cat) use ($search, $selectedCategory) {
                // Query produk untuk kategori ini
                $query = $cat->products()->orderBy('id', 'asc');
                
                // Apply search filter
                if (!empty($search)) {
                    $query->where('name', 'like', "%{$search}%");
                }

                // Ambil produk
                $cat->products = $query->get();
                
                // Tandai kategori aktif
                $cat->isActive = !empty($selectedCategory) && (string)$selectedCategory === (string)$cat->id;
                $cat->count = $cat->products->count();
                
                return $cat;
            });

            // Jika ada filter kategori, hanya tampilkan kategori yang dipilih
            if (!empty($selectedCategory)) {
                $categories = $categories->filter(function($cat) use ($selectedCategory) {
                    return (string)$cat->id === (string)$selectedCategory;
                });
            }
        } else {
            // Fallback untuk skema lama
            $cats = Product::selectRaw('category, min(id) as first_id')
                ->groupBy('category')
                ->orderBy('first_id')
                ->get();

            $categories = $cats->map(function ($c) use ($search, $selectedCategory) {
                $query = Product::where('category', $c->category)->orderBy('id','asc');
                

                
                if (!empty($search)) {
                    $query->where('name', 'like', "%{$search}%");
                }

                $c->products = $query->get();
                $c->id = $c->category;
                $c->name = $c->category;
                $c->count = $c->products->count();
                $c->isActive = !empty($selectedCategory) && (string)$selectedCategory === (string)$c->id;
                
                return $c;
            });

            if (!empty($selectedCategory)) {
                $categories = $categories->filter(function($c) use ($selectedCategory) {
                    return (string)$c->id === (string)$selectedCategory;
                });
            }

            // ✅ NEW: Load all categories untuk filter buttons (fallback mode)
            $allCategories = collect();
        }

        // ✅ FIXED: Tambahkan $allCategories ke compact()
        return view('menu', compact('categories', 'allCategories', 'totalProductsCount', 'search', 'selectedCategory'));
    }
}