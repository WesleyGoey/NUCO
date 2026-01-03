<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Discount;
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
            ->orderBy('id', 'asc')
            ->get();

        // ✅ Get active discounts (current date within period range)
        $today = now()->toDateString();
        $activeDiscounts = Discount::with(['periods' => function($q) use ($today) {
            $q->whereDate('start_date', '<=', $today)
              ->where(function ($q2) use ($today) {
                  $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
              });
        }])
        ->whereHas('periods', function($q) use ($today) {
            $q->whereDate('start_date', '<=', $today)
              ->where(function ($q2) use ($today) {
                  $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
              });
        })
        ->orderBy('name')
        ->get();

        return view('cashier.checkout', compact('orders', 'activeDiscounts'));
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
     * ✅ NEW: Apply discount if selected
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|in:qris,cash',
            'discount_id' => 'nullable|exists:discounts,id',
        ]);

        $cashierId = $request->user()->id;
        $order = Order::findOrFail($request->order_id);

        if ($order->payment()->exists()) {
            return back()->with('error', 'Payment already processed for this order!');
        }

        DB::transaction(function () use ($request, $order, $cashierId) {
            $finalAmount = $order->total_price;

            // ✅ Apply discount if selected
            if ($request->discount_id) {
                $discount = Discount::find($request->discount_id);
                if ($discount) {
                    if ($discount->type === 'percent') {
                        $discountValue = ($order->total_price * $discount->value) / 100;
                    } else {
                        $discountValue = $discount->value;
                    }
                    $finalAmount -= $discountValue;
                    $order->update(['discount_id' => $discount->id]);
                }
            }

            // Create payment record
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $cashierId,
                'amount' => $finalAmount,
                'method' => $request->method,
                'is_available' => true,
                'payment_time' => now(),
            ]);

            // Update order status to completed
            $order->update(['status' => 'completed']);

            // Release table
            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }
        });

        return back()->with('success', 'Payment processed successfully!');
    }
}