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
use Illuminate\Support\Facades\Log;

class CashierController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function checkout(Request $request): View
    {
        // Get orders that:
        // 1. Status = 'sent' (ready for payment)
        // 2. Don't have any payment record yet
        $orders = Order::with(['user', 'table', 'products', 'discount'])
            ->where('status', 'sent')
            ->whereDoesntHave('payment')
            ->orderBy('created_at', 'asc')
            ->get();

        // Get active discounts
        $activeDiscounts = Discount::whereHas('periods', function($q) {
            $today = now()->toDateString();
            $q->where('start_date', '<=', $today)
              ->where(function($q2) use ($today) {
                  $q2->whereNull('end_date')
                     ->orWhere('end_date', '>=', $today);
              });
        })
        ->orderBy('name', 'asc')
        ->get();

        return view('cashier.checkout', compact('orders', 'activeDiscounts'));
    }

    /**
     * Display order history (paid orders).
     */
    public function orderHistory(Request $request): View
    {
        $orders = Order::with(['user', 'table', 'products', 'discount', 'payment'])
            ->whereHas('payment')
            ->orderBy('created_at', 'desc')
            ->paginate(20); // ✅ CHANGED to 20

        return view('cashier.order_history', compact('orders'));
    }

    /**
     * ✅ Generate Midtrans Snap Token TANPA create payment record
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'discount_id' => 'nullable|exists:discounts,id',
        ]);

        $cashierId = $request->user()->id;
        $order = Order::findOrFail($request->order_id);

        // ✅ Check if payment already exists (success/failed)
        if ($order->payment()->exists()) {
            $existingPayment = $order->payment;
            
            if ($existingPayment->status === 'success') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already completed for this order!'
                ], 400);
            }
        }

        $originalAmount = $order->total_price;
        $finalAmount = $originalAmount;
        $discountAmount = 0;
        $appliedDiscount = null;

        // Apply discount if selected
        if ($request->discount_id) {
            $discount = Discount::find($request->discount_id);
            if ($discount) {
                if ($discount->min_order_amount && $originalAmount < $discount->min_order_amount) {
                    return response()->json([
                        'success' => false,
                        'message' => "Minimum order amount is Rp " . number_format($discount->min_order_amount, 0, ',', '.')
                    ], 400);
                }

                if ($discount->type === 'percent') {
                    $discountAmount = ($originalAmount * $discount->value) / 100;
                } else {
                    $discountAmount = $discount->value;
                }
                
                $finalAmount = $originalAmount - $discountAmount;
                $appliedDiscount = $discount;
                
                $order->update(['discount_id' => $discount->id]);
            }
        }

        // Generate unique transaction ID
        $transactionId = 'ORDER-' . $order->id . '-' . time();

        // Midtrans params
        $params = [
            'transaction_details' => [
                'order_id' => $transactionId,
                'gross_amount' => (int) $finalAmount,
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

        if ($appliedDiscount && $discountAmount > 0) {
            $params['item_details'][] = [
                'id' => 'DISCOUNT-' . $appliedDiscount->id,
                'price' => -1 * (int) $discountAmount,
                'quantity' => 1,
                'name' => 'Discount: ' . $appliedDiscount->name,
            ];
        }

        try {
            $snapToken = Snap::getSnapToken($params);

            // ✅ JANGAN CREATE PAYMENT DI SINI!
            // Payment akan dibuat setelah user bayar sukses (di frontend atau callback)

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'transaction_id' => $transactionId,
                'order_id' => $order->id, // ✅ Return order_id untuk create payment nanti
                'cashier_id' => $cashierId, // ✅ Return cashier_id
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'discount_name' => $appliedDiscount ? $appliedDiscount->name : null,
            ]);

        } catch (\Exception $e) {
            // ✅ ADD: Log error for debugging
            Log::error('Midtrans Snap Token Error', [
                'order_id' => $order->id,
                'amount' => $finalAmount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payment token. Please try again.'
            ], 500);
        }
    }

    /**
     * ✅ Store payment record setelah user bayar sukses
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric',
            'cashier_id' => 'required|exists:users,id',
            'snap_token' => 'nullable|string',
        ]);

        $order = Order::find($request->order_id);

        // ✅ Check if payment already exists by transaction_id
        if (Payment::where('transaction_id', $request->transaction_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already recorded for this transaction'
            ], 400);
        }

        // Check if payment already exists for order
        if ($order->payment()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already recorded'
            ], 400);
        }

        // ✅ Use DB transaction untuk atomicity
        DB::transaction(function () use ($order, $request) {
            // Create payment record
            Payment::create([
                'order_id' => $request->order_id,
                'user_id' => $request->cashier_id,
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'snap_token' => $request->snap_token,
                'status' => 'success',
                'payment_time' => now(),
            ]);

            // Update order status to completed
            $order->update(['status' => 'completed']);

            // ✅ Release table (status = 'available')
            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
        ]);
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

        // Extract order ID from transaction_id (ORDER-123-1234567890)
        preg_match('/ORDER-(\d+)-/', $orderId, $matches);
        $extractedOrderId = $matches[1] ?? null;

        if (!$extractedOrderId) {
            return response()->json(['message' => 'Invalid transaction ID format'], 400);
        }

        $order = Order::find($extractedOrderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function() use ($order, $transactionStatus, $orderId, $request) {
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                // ✅ Payment success - create payment record if not exists
                if (!$order->payment()->exists()) {
                    Payment::create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'amount' => $request->gross_amount,
                        'transaction_id' => $orderId,
                        'snap_token' => null,
                        'status' => 'success',
                        'payment_time' => now(),
                    ]);
                }

                // Update order status to completed
                $order->update(['status' => 'completed']);

                // ✅ Release table (status = 'available')
                if ($order->table) {
                    $order->table->update(['status' => 'available']);
                }
            }
        });

        return response()->json(['message' => 'Callback handled']);
    }
}