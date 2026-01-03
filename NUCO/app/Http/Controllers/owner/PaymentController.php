<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(): View
    {
        // ✅ CHANGED: Only total revenue, no breakdown
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $totalTransactions = Payment::where('status', 'success')->count();
        $pendingTransactions = Payment::where('status', 'pending')->count();

        // Transaction log (newest first)
        $payments = Payment::with(['order', 'user'])
            ->orderBy('payment_time', 'desc')
            ->paginate(30);

        return view('owner.payments.index', compact(
            'totalRevenue',
            'totalTransactions',
            'pendingTransactions',
            'payments'
        ));
    }
}