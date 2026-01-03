<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $selectedCategoryId = $request->query('category', null);

        $cart = session('cart', []);
        $selectedTable = session('selected_table');

        // ✅ Calculate total products count
        $totalProductsCount = Category::with('products')->get()->sum(fn($c) => $c->products->where('is_available', 1)->count());
        
        // ✅ Get all categories for filter buttons
        $allCategoriesForButtons = Category::orderBy('id')->get();

        $categories = Category::with(['products' => function($q) use ($search) {
            $q->where('is_available', 1)->orderBy('id', 'asc');
            if (!empty($search)) {
                $q->where('name', 'like', "%{$search}%");
            }
        }])->orderBy('id', 'asc')->get();

        foreach ($categories as $c) {
            if (!empty($selectedCategoryId) && $c->id != $selectedCategoryId) {
                $c->products = collect();
                continue;
            }

            $query = $c->products()->where('is_available', 1)->orderBy('id', 'asc');
            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }
            $c->products = $query->get();

            // Check ingredient stocks
            $c->products->load('ingredients');
            foreach ($c->products as $p) {
                if ($p->ingredients->isEmpty()) {
                    $p->in_stock = true;
                    continue;
                }
                $ok = true;
                foreach ($p->ingredients as $ing) {
                    if ($ing->current_stock < $ing->pivot->amount_needed) {
                        $ok = false;
                        break;
                    }
                }
                $p->in_stock = $ok;
            }

            $c->count = $c->products->count();
        }

        return view('waiter.cart', compact(
            'cart',
            'selectedTable',
            'categories',
            'search',
            'selectedCategoryId',
            'allCategoriesForButtons',
            'totalProductsCount'
        ));
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        $cart = session('cart', []);
        $productId = $product->id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
            $cart[$productId]['subtotal'] = $cart[$productId]['quantity'] * $cart[$productId]['price'];
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
                'notes' => '',
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', "{$product->name} added to cart!");
    }

    // ✅ CORRECT: updateQuantity() method is COMPLETE
    public function updateQuantity(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'action' => 'required|in:increase,decrease',
        ]);

        $cart = session('cart', []);
        $productId = $request->product_id;

        if (!isset($cart[$productId])) {
            return back()->with('error', 'Product not found in cart.');
        }

        if ($request->action === 'increase') {
            $cart[$productId]['quantity'] += 1;
        } elseif ($request->action === 'decrease') {
            $cart[$productId]['quantity'] -= 1;
            
            if ($cart[$productId]['quantity'] <= 0) {
                unset($cart[$productId]);
                session(['cart' => $cart]);
                return back()->with('success', 'Product removed from cart.');
            }
        }

        $cart[$productId]['subtotal'] = $cart[$productId]['quantity'] * $cart[$productId]['price'];
        session(['cart' => $cart]);

        return back()->with('success', 'Cart updated!');
    }

    public function updateNotes(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            $cart[$productId]['notes'] = $request->notes ?? '';
            session(['cart' => $cart]);
            return back()->with('success', 'Notes updated!');
        }

        return back()->with('error', 'Product not found in cart.');
    }

    public function remove(Request $request): RedirectResponse
    {
        $request->validate(['product_id' => 'required|integer']);

        $cart = session('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
            return back()->with('success', 'Product removed from cart!');
        }

        return back()->with('error', 'Product not found in cart.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared successfully!');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cart = session('cart', []);
        $selectedTable = session('selected_table');

        if (empty($cart)) {
            return back()->with('error', 'Cart is empty!');
        }

        if (!$selectedTable) {
            return back()->with('error', 'No table selected!');
        }

        $table = RestaurantTable::find($selectedTable['id']);
        if (!$table) {
            return back()->with('error', 'Table not found!');
        }

        $totalAmount = array_sum(array_column($cart, 'subtotal'));

        $user = Auth::user();
        $userId = $user ? $user->id : null;

        $order = Order::create([
            'restaurant_table_id' => $table->id,
            'user_id' => $userId,
            'total_price' => $totalAmount,
            'status' => 'pending',
        ]);

        foreach ($cart as $item) {
            $order->products()->attach($item['id'], [
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
                'note' => $item['notes'] ?? null,
            ]);
        }

        $table->update(['status' => 'occupied']);

        session()->forget(['cart', 'selected_table']);

        return redirect()->route('waiter.tables')->with('success', "Order #{$order->id} created successfully!");
    }
}