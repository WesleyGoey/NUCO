<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Category;
use App\Models\Product;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $selectedCategory = $request->query('category', null);

        // Check if Category model exists
        if (class_exists(Category::class)) {
            $categories = Category::with(['products' => function ($query) use ($search) {
                $query->where('is_available', 1)->orderBy('id', 'asc');
                if (!empty($search)) {
                    $query->where('name', 'like', "%{$search}%");
                }
            }])
            ->orderBy('id')
            ->get();

            // Count products in each category
            foreach ($categories as $cat) {
                $query = $cat->products()->where('is_available', 1)->orderBy('id', 'asc');
                if (!empty($search)) {
                    $query->where('name', 'like', "%{$search}%");
                }

                // Ambil produk
                $cat->products = $query->get();

                // ✅ NEW: Check ingredient stocks and set in_stock on each product
                $cat->products->load('ingredients');
                foreach ($cat->products as $p) {
                    if ($p->ingredients->isEmpty()) {
                        $p->in_stock = true;
                        continue;
                    }
                    $ok = true;
                    foreach ($p->ingredients as $ing) {
                        $needed = $ing->pivot->amount_needed;
                        if ($ing->current_stock < $needed) {
                            $ok = false;
                            break;
                        }
                    }
                    $p->in_stock = $ok;
                }
                // ✅ END NEW

                // Tandai kategori aktif
                $cat->count = $cat->products->count();
            }

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
                return $c;
            });

            if (!empty($selectedCategory)) {
                $categories = $categories->filter(function($cat) use ($selectedCategory) {
                    return $cat->id === $selectedCategory;
                });
            }
        }

        $allCategoriesForButtons = Category::orderBy('id')->get();

        return view('menu', compact('categories', 'search', 'selectedCategory', 'allCategoriesForButtons'));
    }
}