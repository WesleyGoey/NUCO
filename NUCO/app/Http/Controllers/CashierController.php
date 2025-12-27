<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashierController extends Controller
{
    /**
     * Display all unpaid orders for checkout.
     */
    public function checkout(Request $request): View
    {
        // Get orders that don't have payments (unpaid)
        $orders = Order::with(['user', 'table', 'products', 'discount'])
            ->whereDoesntHave('payment')
            ->whereIn('status', ['completed', 'processing'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashier.checkout', compact('orders'));
    }

    /**
     * Display order history (paid orders).
     */
    public function orderHistory(Request $request): View
    {
        // Get orders that have payments (paid orders)
        $orders = Order::with(['user', 'table', 'products', 'discount', 'payment.user'])
            ->whereHas('payment')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('cashier.order_history', compact('orders'));
    }

    /**
     * Process payment for an order.
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|in:cash,card,qris',
            'amount' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Check if payment already exists
        if ($order->payment()->exists()) {
            return back()->with('error', 'This order has already been paid.');
        }

        DB::transaction(function () use ($request, $order) {
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'method' => $request->method,
                'payment_time' => now(),
            ]);

            // Update order status to completed if not already
            if ($order->status !== 'completed') {
                $order->update(['status' => 'completed']);
            }
        });

        return back()->with('success', 'Payment processed successfully!');
    }
}