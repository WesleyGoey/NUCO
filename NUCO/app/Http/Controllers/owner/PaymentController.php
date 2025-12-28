<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(): View
    {
        // Hitung total omzet per metode
        $totalRevenue = Payment::sum('amount');
        $qrisRevenue = Payment::where('method', 'qris')->sum('amount');
        $cashRevenue = Payment::where('method', 'cash')->sum('amount');

        // Status ketersediaan metode (ambil dari setting atau default true)
        $qrisAvailable = Payment::where('method', 'qris')->value('is_available') ?? true;
        $cashAvailable = Payment::where('method', 'cash')->value('is_available') ?? true;

        // Log transaksi (newest first)
        $payments = Payment::with(['order', 'user'])
            ->orderBy('payment_time', 'desc')
            ->paginate(30);

        return view('owner.payments.index', compact(
            'totalRevenue',
            'qrisRevenue', 
            'cashRevenue',
            'qrisAvailable',
            'cashAvailable',
            'payments'
        ));
    }

    public function toggleMethod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', 'in:qris,cash'],
            'is_available' => ['required', 'boolean'],
        ]);

        // Update semua payment dengan method tersebut
        Payment::where('method', $validated['method'])
            ->update(['is_available' => $validated['is_available']]);

        $status = $validated['is_available'] ? 'enabled' : 'disabled';
        $methodLabel = strtoupper($validated['method']);

        return redirect()->route('owner.payments.index')
            ->with('success', "{$methodLabel} payment method {$status} successfully!");
    }
}