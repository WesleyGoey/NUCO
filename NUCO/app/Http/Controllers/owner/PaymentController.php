<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with(['order','user'])->orderBy('payment_time','desc')->paginate(30);
        return view('owner.payments.index', compact('payments'));
    }
}