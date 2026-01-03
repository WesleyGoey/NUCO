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
              ->where(function($q2) use ($today) {
                  $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
              });
        }])
        ->whereHas('periods', function($q) use ($today) {
            $q->whereDate('start_date', '<=', $today)
              ->where(function($q2) use ($today) {
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
            'amount' => 'required|numeric|min:0',
            'discount_id' => 'nullable|exists:discounts,id', // ✅ NEW: optional discount
        ]);

        $cashierId = $request->user()->id;
        $order = Order::findOrFail($request->order_id);

        if ($order->payment()->exists()) {
            return back()->with('error', 'This order has already been paid.');
        }

        DB::transaction(function () use ($request, $order, $cashierId) {
            // ✅ Apply discount if provided
            $finalAmount = $request->amount;
            $discountId = $request->discount_id;

            if ($discountId) {
                $discount = Discount::find($discountId);
                
                if ($discount) {
                    // Check minimum order requirement
                    if ($discount->min_order_amount && $order->total_price < $discount->min_order_amount) {
                        throw new \Exception("Order does not meet minimum amount for this discount (Rp " . number_format($discount->min_order_amount, 0, ',', '.') . ")");
                    }

                    // Calculate discount
                    if ($discount->type === 'percent') {
                        $discountAmount = ($order->total_price * $discount->value) / 100;
                    } else {
                        $discountAmount = $discount->value;
                    }

                    $finalAmount = max(0, $order->total_price - $discountAmount);

                    // Update order with discount
                    $order->update([
                        'discount_id' => $discountId,
                        'total_price' => $finalAmount,
                    ]);
                }
            }

            // Create payment
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $cashierId,
                'amount' => $finalAmount,
                'method' => $request->method,
                'payment_time' => now(),
            ]);

            // Mark order as completed
            if ($order->status !== 'completed') {
                $order->update(['status' => 'completed']);
            }
        });

        return back()->with('success', 'Payment processed successfully!');
    }
}