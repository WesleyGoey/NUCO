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

        // ✅ Calculate statistics
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $totalTransactions = Payment::where('status', 'success')->count(); // ✅ FIXED: Changed variable name

        return view('owner.payments.index', compact('payments', 'totalRevenue', 'totalTransactions'));
    }
}