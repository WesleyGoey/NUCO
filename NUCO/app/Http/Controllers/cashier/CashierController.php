<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Midtrans\Config;
use Midtrans\Snap;

class CashierController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Display all unpaid orders for checkout.
     */
    public function checkout(Request $request): View
    {
        // Get orders that don't have payments (unpaid)
        $orders = Order::with(['user', 'table', 'products', 'discount'])
            ->whereDoesntHave('payment')
            ->whereIn('status', ['ready', 'completed'])
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
            ->whereHas('payment', function($q) {
                $q->where('status', 'success'); // ✅ ONLY show successful payments
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('cashier.order_history', compact('orders'));
    }

    /**
     * ✅ Generate Midtrans Snap Token with Discount Applied
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'discount_id' => 'nullable|exists:discounts,id',
        ]);

        $cashierId = $request->user()->id;
        $order = Order::findOrFail($request->order_id);

        if ($order->payment()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already processed for this order!'
            ], 400);
        }

        $originalAmount = $order->total_price;
        $finalAmount = $originalAmount;
        $discountAmount = 0;
        $appliedDiscount = null;

        // ✅ Apply discount if selected
        if ($request->discount_id) {
            $discount = Discount::find($request->discount_id);
            if ($discount) {
                // Check minimum order amount
                if ($discount->min_order_amount && $originalAmount < $discount->min_order_amount) {
                    return response()->json([
                        'success' => false,
                        'message' => "Minimum order amount is Rp " . number_format($discount->min_order_amount, 0, ',', '.')
                    ], 400);
                }

                // Calculate discount
                if ($discount->type === 'percent') {
                    $discountAmount = ($originalAmount * $discount->value) / 100;
                } else {
                    $discountAmount = $discount->value;
                }
                
                $finalAmount = $originalAmount - $discountAmount;
                $appliedDiscount = $discount;
                
                // ✅ IMPORTANT: Update order dengan discount_id
                $order->update(['discount_id' => $discount->id]);
            }
        }

        // Generate unique transaction ID
        $transactionId = 'ORDER-' . $order->id . '-' . time();

        // ✅ Create Midtrans transaction parameters dengan FINAL AMOUNT (after discount)
        $params = [
            'transaction_details' => [
                'order_id' => $transactionId,
                'gross_amount' => (int) $finalAmount, // ✅ AMOUNT AFTER DISCOUNT
            ],
            'customer_details' => [
                'first_name' => $order->order_name ?? 'Guest',
                'email' => $order->user->email ?? 'guest@nuco.com',
                'phone' => $order->user->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'ORDER-' . $order->id,
                    'price' => (int) $originalAmount,
                    'quantity' => 1,
                    'name' => 'Order #' . $order->id . ' - ' . $order->products->count() . ' items',
                ],
            ],
        ];

        // ✅ Add discount as separate line item (for transparency in Midtrans dashboard)
        if ($appliedDiscount && $discountAmount > 0) {
            $params['item_details'][] = [
                'id' => 'DISCOUNT-' . $appliedDiscount->id,
                'price' => -1 * (int) $discountAmount, // ✅ NEGATIVE AMOUNT
                'quantity' => 1,
                'name' => 'Discount: ' . $appliedDiscount->name,
            ];
        }

        try {
            // ✅ Get Snap token from Midtrans
            $snapToken = Snap::getSnapToken($params);

            // ✅ Create payment record with FINAL AMOUNT (after discount)
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $cashierId,
                'amount' => $finalAmount, // ✅ FINAL AMOUNT
                'transaction_id' => $transactionId,
                'snap_token' => $snapToken,
                'status' => 'pending',
                'payment_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'transaction_id' => $transactionId,
                'payment_id' => $payment->id, // ✅ ADDED: Return payment ID untuk cancel
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'discount_name' => $appliedDiscount ? $appliedDiscount->name : null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payment token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NEW: Cancel pending payment (when user closes popup)
     */
    public function cancelPayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::find($request->payment_id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        // Only delete if still pending
        if ($payment->status === 'pending') {
            $payment->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment cancelled'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment cannot be cancelled'
        ], 400);
    }

    /**
     * ✅ Handle Midtrans callback/notification
     */
    public function handleCallback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;

        $payment = Payment::where('transaction_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        DB::transaction(function() use ($payment, $transactionStatus) {
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                // ✅ Payment success
                $payment->update([
                    'status' => 'success',
                    'payment_time' => now(),
                ]);

                $order = $payment->order;
                $order->update(['status' => 'completed']);

                // Release table
                if ($order->table) {
                    $order->table->update(['status' => 'available']);
                }
            } elseif ($transactionStatus == 'pending') {
                $payment->update(['status' => 'pending']);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $payment->update(['status' => 'failed']);
            }
        });

        return response()->json(['message' => 'Callback handled']);
    }
}