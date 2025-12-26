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
        // Waiter default tab should be 'ready'
        $defaultFilter = ($user && method_exists($user, 'isWaiter') && $user->isWaiter()) ? 'ready' : 'all';
        $filter = $request->query('status', $defaultFilter);

        // counts per status for tabs
        $counts = Order::select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $query = Order::with(['user', 'table', 'products']);

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        // Order by status priority (pending -> processing -> ready -> sent -> completed)
        // then by created_at ascending (oldest first so waiters see oldest orders on top)
        $statusOrderSql = "CASE
            WHEN status = 'pending' THEN 1
            WHEN status = 'processing' THEN 2
            WHEN status = 'ready' THEN 3
            WHEN status = 'sent' THEN 4
            WHEN status = 'completed' THEN 5
            ELSE 6 END";

        $orders = $query
            ->orderByRaw($statusOrderSql)
            ->orderBy('created_at', 'asc')
            ->paginate(20)
            ->withQueryString();

        if ($user && method_exists($user, 'isWaiter') && $user->isWaiter()) {
            return view('orders', compact('orders', 'counts', 'filter'));
        }

        return view('orders', compact('orders', 'counts', 'filter'));
    }

    /**
     * Mark order -> processing
     */
    public function markProcessing(Order $order): RedirectResponse
    {
        if ($order->status === 'pending') {
            $order->update(['status' => 'processing']);
        }

        return redirect()->route('orders', ['status' => 'processing'])->with('success', 'Order marked processing.');
    }

    /**
     * Mark order -> sent (delivered to customer)
     */
    public function markSent(Order $order): RedirectResponse
    {
        // allow sending only when order is 'ready'
        if ($order->status === 'ready') {
            $order->update(['status' => 'sent']);
        }

        return redirect()->route('orders', ['status' => 'sent'])->with('success', 'Order marked sent.');
    }

    /**
     * Mark order -> completed (after payment)
     */
    public function markCompleted(Order $order): RedirectResponse
    {
        if (in_array($order->status, ['sent','processing','pending'])) {
            $order->update(['status' => 'completed']);
        }

        return redirect()->route('orders', ['status' => 'completed'])->with('success', 'Order marked completed.');
    }

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