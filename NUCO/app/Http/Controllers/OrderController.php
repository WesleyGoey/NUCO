<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display list of orders, optional filter by status tab.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Default filter per role:
        // - waiter -> 'ready' (existing behavior)
        // - chef   -> 'pending' (requested)
        // - otherwise -> 'all'
        $defaultFilter = 'all';
        if ($user && method_exists($user, 'isWaiter') && $user->isWaiter()) {
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

        // add total 'all' count so "All" shows a badge too
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
            ELSE 6 END";

        if ($filter === 'all') {
            $orders = $query->orderBy('id', 'asc')->paginate(20)->withQueryString();
        } else {
            $orders = $query
                ->orderByRaw($statusOrderSql)
                ->orderBy('id', 'asc')
                ->paginate(20)
                ->withQueryString();
        }

        return view('orders', compact('orders', 'counts', 'filter'));
    }

    /**
     * Mark order -> processing (chef action "Process")
     */
    public function markProcessing(Order $order): RedirectResponse
    {
        // only allow moving from 'pending' -> 'processing'
        if ($order->status === 'pending') {
            $order->update(['status' => 'processing']);
            return redirect()->route('orders', ['status' => 'processing'])->with('success', "Order #{$order->id} moved to processing.");
        }

        return back()->with('error', "Order #{$order->id} cannot be processed from status '{$order->status}'.");
    }

    /**
     * Mark order -> ready (chef action "Ready")
     */
    public function markReady(Order $order): RedirectResponse
    {
        // only allow moving from 'processing' -> 'ready'
        if ($order->status === 'processing') {
            $order->update(['status' => 'ready']);
            return redirect()->route('orders', ['status' => 'ready'])->with('success', "Order #{$order->id} marked ready.");
        }

        return back()->with('error', "Order #{$order->id} cannot be marked ready from status '{$order->status}'.");
    }

    /**
     * Mark order -> sent (existing)
     */
    public function markSent(Order $order): RedirectResponse
    {
        if ($order->status === 'ready') {
            $order->update(['status' => 'sent']);
        }

        return redirect()->route('orders', ['status' => 'sent'])->with('success', 'Order marked sent.');
    }

    /**
     * Mark order -> completed (existing)
     */
    public function markCompleted(Order $order): RedirectResponse
    {
        if (in_array($order->status, ['sent','processing','pending'])) {
            $order->update(['status' => 'completed']);
        }

        return redirect()->route('orders', ['status' => 'completed'])->with('success', 'Order marked completed.');
    }

    /**
     * Show single order (existing)
     */
    public function show(Order $order): View
    {
        $order->load(['user','table','products']);
        return view('orders.show', compact('order'));
    }

    public function pay(Order $order): RedirectResponse
    {
        // minimal: mark order as completed (adjust business logic as needed)
        if ($order->status !== 'completed') {
            $order->update(['status' => 'completed']);
        }

        return redirect()->route('orders')->with('success', 'Order marked as paid.');
    }
}