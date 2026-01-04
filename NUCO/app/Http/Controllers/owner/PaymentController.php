<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with(['order.user', 'order.table'])
            ->orderBy('id', 'asc')
            ->paginate(20);

        // ✅ FIXED: Use 'status' instead of 'payment_status'
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $successfulTransactions = Payment::where('status', 'success')->count();

        return view('owner.payments.index', compact('payments', 'totalRevenue', 'successfulTransactions'));
    }
}