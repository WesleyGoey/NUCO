<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    // Show menu + cart for waiter (same view: waiter.cart)
    public function index(Request $request): View
    {
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

        $productsQuery = Product::query();
        if ($request->filled('category')) {
            $productsQuery->where('category', $request->query('category'));
        }
        if (!empty($search)) {
            $products = $productsQuery->where('name', 'like', "%{$search}%")
                ->orderBy('id', 'asc')
                ->paginate(3)
                ->withQueryString();
        } else {
            $products = $productsQuery->orderBy('id', 'asc')->get();
        }

        $selectedTable = session('selected_table') ?? null;
        $cart = session('waiter_cart', []);
        $cartCount = array_sum(array_map(fn($i)=>(int)($i['quantity'] ?? 0), $cart ?: []));
        $cartTotal = array_sum(array_map(fn($i)=>(int)($i['subtotal'] ?? 0), $cart ?: []));

        return view('waiter.cart', compact('products','categories','totalProductsCount','search','selectedTable','cart','cartCount','cartTotal'));
    }

    // Add item (or clear)
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

        // key by product id
        $key = (string)$product->id;
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $qty;
            $cart[$key]['subtotal'] = $cart[$key]['quantity'] * $product->price;
        } else {
            $cart[$key] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $qty,
                'subtotal' => $product->price * $qty,
            ];
        }

        session(['waiter_cart' => $cart]);

        return redirect()->route('waiter.cart');
    }

    // Optional: update or remove specific item
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session('waiter_cart', []);
        $key = (string)$data['product_id'];

        if (!isset($cart[$key])) {
            return redirect()->route('waiter.cart');
        }

        if ($data['quantity'] <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = $data['quantity'];
            $cart[$key]['subtotal'] = $cart[$key]['quantity'] * $cart[$key]['price'];
        }

        session(['waiter_cart' => $cart]);
        return redirect()->route('waiter.cart');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('waiter_cart');
        return redirect()->route('waiter.cart');
    }
}