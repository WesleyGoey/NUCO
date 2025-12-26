<?php

namespace App\Http\Controllers\waiter;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $selectedCategory = $request->query('category', '');

        $hasCategoryId = Schema::hasColumn('products', 'category_id');
        $totalProductsCount = Product::count();

        if ($hasCategoryId) {
            $allCategories = Category::orderBy('id')->get();

            $categories = $allCategories->map(function ($cat) use ($search, $selectedCategory) {
                $query = $cat->products()->orderBy('id', 'asc');
                
                if (!empty($search)) {
                    $query->where('name', 'like', "%{$search}%");
                }

                $cat->products = $query->get();
                $cat->isActive = !empty($selectedCategory) && (string)$selectedCategory === (string)$cat->id;
                $cat->count = $cat->products->count();
                
                return $cat;
            });

            if (!empty($selectedCategory)) {
                $categories = $categories->filter(function($cat) use ($selectedCategory) {
                    return (string)$cat->id === (string)$selectedCategory;
                });
            }
        } else {
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
        }

        $selectedTable = session('selected_table') ?? null;
        $cart = session('waiter_cart', []);
        $cartCount = array_sum(array_map(fn($i)=>(int)($i['quantity'] ?? 0), $cart ?: []));
        $cartTotal = array_sum(array_map(fn($i)=>(int)($i['subtotal'] ?? 0), $cart ?: []));

        return view('waiter.cart', compact('categories', 'totalProductsCount', 'search', 'selectedCategory', 'selectedTable', 'cart', 'cartCount', 'cartTotal'));
    }

    public function updateNote(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'note' => 'nullable|string|max:500',
        ]);

        $cart = session('waiter_cart', []);
        $key = (string)$data['product_id'];

        if (isset($cart[$key])) {
            $cart[$key]['note'] = $data['note'] ?? '';
            session(['waiter_cart' => $cart]);
        }

        return redirect()->route('waiter.cart');
    }

    public function add(Request $request): RedirectResponse
    {
        if ($request->input('clear')) {
            session()->forget('waiter_cart');
            return redirect()->route('waiter.cart');
        }

        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'sometimes|integer|min:1'
        ]);

        $product = Product::find($data['product_id']);
        $qty = (int) ($data['quantity'] ?? 1);

        $cart = session('waiter_cart', []);
        $key = (string)$product->id;
        
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $qty;
            $cart[$key]['subtotal'] = $cart[$key]['quantity'] * $product->price;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $qty,
                'subtotal' => $qty * $product->price,
                'note' => '',
            ];
        }

        session(['waiter_cart' => $cart]);
        return redirect()->route('waiter.cart');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session('waiter_cart', []);
        $key = (string)$data['product_id'];

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $data['quantity'];
            $cart[$key]['subtotal'] = $cart[$key]['quantity'] * $cart[$key]['price'];
            session(['waiter_cart' => $cart]);
        }

        return redirect()->route('waiter.cart');
    }

    public function remove(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $cart = session('waiter_cart', []);
        $key = (string)$data['product_id'];

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session(['waiter_cart' => $cart]);
        }

        return redirect()->route('waiter.cart');
    }

    public function clear(): RedirectResponse
    {
        // Only clear the cart for "Clear Cart" action.
        // Do not release selected table or navigate away.
        session()->forget('waiter_cart');
        return redirect()->route('waiter.cart')->with('success', 'Cart cleared.');
    }
}