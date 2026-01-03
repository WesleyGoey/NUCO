<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Default filter per role
        $defaultFilter = 'all';
        
        if ($user && method_exists($user, 'isOwner') && $user->isOwner()) {
            $defaultFilter = 'all';
        } elseif ($user && method_exists($user, 'isWaiter') && $user->isWaiter()) {
            $defaultFilter = 'ready';
        } elseif ($user && method_exists($user, 'isChef') && $user->isChef()) {
            $defaultFilter = 'pending';
        }

        $filter = $request->query('status', $defaultFilter);

        // counts per status for tabs
        $counts = Order::select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $counts['all'] = Order::count();

        $query = Order::with(['user', 'table', 'products']);

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $statusOrderSql = "CASE
            WHEN status = 'pending' THEN 1
            WHEN status = 'processing' THEN 2
            WHEN status = 'ready' THEN 3
            WHEN status = 'sent' THEN 4
            WHEN status = 'completed' THEN 5
            ELSE 6
        END";

        if ($filter === 'all') {
            $orders = $query->orderByRaw($statusOrderSql)->orderBy('id', 'asc')->paginate(20)->withQueryString();
        } else {
            $orders = $query->orderBy('id', 'asc')->paginate(20)->withQueryString();
        }

        return view('orders', compact('orders', 'counts', 'filter'));
    }

    public function markProcessing(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be processed.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'processing']);

            // ✅ Deduct ingredient stock when order starts processing
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;

                foreach ($product->ingredients as $ingredient) {
                    $amountNeeded = $ingredient->pivot->amount_needed * $quantity;
                    
                    $ingredient->decrement('current_stock', $amountNeeded);

                    // Log inventory change
                    InventoryLog::create([
                        'ingredient_id' => $ingredient->id,
                        'user_id' => Auth::id(),
                        'change_amount' => -$amountNeeded,
                        'type' => 'consumption',
                    ]);
                }
            }
        });

        return redirect()->route('orders', ['status' => 'processing'])
            ->with('success', "Order #{$order->id} is now being processed!");
    }

    public function markReady(Order $order): RedirectResponse
    {
        if ($order->status === 'processing') {
            $order->update(['status' => 'ready']);
            return redirect()->route('orders', ['status' => 'ready'])
                ->with('success', "Order #{$order->id} is now ready!");
        }

        return back()->with('error', 'Order must be in processing status.');
    }

    public function markSent(Order $order): RedirectResponse
    {
        if ($order->status === 'ready') {
            $order->update(['status' => 'sent']);
            return redirect()->route('orders', ['status' => 'sent'])
                ->with('success', "Order #{$order->id} has been sent!");
        }

        return back()->with('error', 'Order must be in ready status.');
    }

    public function markCompleted(Order $order): RedirectResponse
    {
        if ($order->status === 'sent') {
            $order->update(['status' => 'completed']);
            return redirect()->route('orders', ['status' => 'completed'])
                ->with('success', "Order #{$order->id} is completed!");
        }

        return back()->with('error', 'Order must be in sent status.');
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'table', 'products', 'discount', 'payment']);
        return view('orders.show', compact('order'));
    }

    public function pay(Order $order): RedirectResponse
    {
        if ($order->payment()->exists()) {
            return back()->with('error', 'Order already paid!');
        }

        return redirect()->route('cashier.checkout')->with('info', 'Please process payment for Order #' . $order->id);
    }
}