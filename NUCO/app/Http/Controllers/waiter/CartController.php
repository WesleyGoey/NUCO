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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // ✅ Step 1: Log request data
        Log::info('Checkout started', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
        ]);

        $cart = session('cart', []);
        $selectedTable = session('selected_table');

        // ✅ Step 2: Log session data
        Log::info('Session data', [
            'cart' => $cart,
            'selected_table' => $selectedTable
        ]);

        if (empty($cart)) {
            Log::warning('Checkout failed: Empty cart');
            return back()->with('error', 'Your cart is empty!');
        }

        if (!$selectedTable) {
            Log::warning('Checkout failed: No table selected');
            return back()->with('error', 'No table selected!');
        }

        $total = array_sum(array_map(fn($i) => (int)($i['subtotal'] ?? 0), $cart));

        // ✅ Step 3: Log calculated total
        Log::info('Cart total calculated', [
            'total' => $total,
            'cart_items' => count($cart)
        ]);

        try {
            $orderId = null;

            // ✅ Use transaction with error handling
            DB::transaction(function () use ($validated, $cart, $selectedTable, $total, &$orderId) {
                // ✅ Step 4: Lock the table
                $table = RestaurantTable::where('id', $selectedTable['id'])
                    ->lockForUpdate()
                    ->first();

                if (!$table) {
                    Log::error('Table not found', ['table_id' => $selectedTable['id']]);
                    throw new \Exception('Table not found');
                }

                Log::info('Table locked', [
                    'table_id' => $table->id,
                    'table_number' => $table->table_number
                ]);

                // ✅ Step 5: Create order
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'restaurant_table_id' => $selectedTable['id'],
                    'order_name' => $validated['customer_name'],
                    'total_price' => $total,
                    'status' => 'pending',
                ]);

                $orderId = $order->id;

                Log::info('Order created', [
                    'order_id' => $order->id,
                    'order_name' => $order->order_name,
                    'total_price' => $order->total_price,
                    'status' => $order->status
                ]);

                // ✅ Step 6: Attach products to order
                foreach ($cart as $productId => $item) {
                    $order->products()->attach($productId, [
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                        'note' => $item['note'] ?? null,
                    ]);

                    Log::info('Product attached', [
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal']
                    ]);
                }

                // ✅ Log successful transaction
                Log::info('Order transaction completed successfully', [
                    'order_id' => $order->id,
                    'table_id' => $table->id,
                    'total' => $total,
                    'products_count' => count($cart)
                ]);
            });

            // ✅ Step 7: Clear cart dan selected_table dari session
            session()->forget(['cart', 'selected_table']);

            Log::info('Session cleared after checkout', [
                'order_id' => $orderId
            ]);

            // ✅ Step 8: Redirect ke waiter.tables
            return redirect()->route('waiter.tables')
                ->with('success', 'Order placed successfully! Table remains occupied until payment.');

        } catch (\Exception $e) {
            // ✅ Log error for debugging
            Log::error('Checkout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'table_id' => $selectedTable['id'] ?? null,
                'cart_items' => count($cart),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }
}