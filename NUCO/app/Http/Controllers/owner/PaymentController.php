<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(): View
    {
        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $totalTransactions = Payment::where('status', 'success')->count();

        // Transaction log (newest first)
        $payments = Payment::with(['order', 'user'])
            ->orderBy('payment_time', 'desc')
            ->paginate(30);

        return view('owner.payments.index', compact(
            'totalRevenue',
            'totalTransactions',
            'payments'
        ));
    }
}