@extends('layouts.mainlayout')

@section('title', 'Payments')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold" style="color:#4b3028;">Payments Management</h3>
            <div class="small text-muted">Revenue overview & transactions</div>
        </div>
        <div class="text-end">
            <div class="small text-muted">Total Transactions</div>
            <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">{{ $payments->total() }}</div>
        </div>
    </div>

    {{-- Revenue Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Revenue --}}
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:12px; border-left:4px solid #A4823B;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-muted mb-1">Total Revenue</div>
                            <div class="fw-bold" style="color:#A4823B; font-size:1.5rem;">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </div>
                        </div>
                        <div style="width:48px;height:48px;border-radius:10px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-wallet2" style="font-size:1.5rem;color:#A4823B;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Successful Transactions --}}
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius:12px; border-left:4px solid #2D7A3B;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-muted mb-1">Successful Payments</div>
                            <div class="fw-bold" style="color:#2D7A3B; font-size:1.5rem;">
                                {{ $totalTransactions }}
                            </div>
                        </div>
                        <div style="width:48px;height:48px;border-radius:10px;background:#E6F9EE;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-check-circle" style="font-size:1.5rem;color:#2D7A3B;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ REMOVED: Pending Transactions Card --}}
    </div>

    {{-- Transaction Log --}}
    <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
        <div class="card-header" style="background:#F5F0E5; padding:16px;">
            <h5 class="m-0 fw-bold" style="color:#4b3028;">Transaction Log</h5>
            <div class="small text-muted">All payment transactions</div>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead style="background:#F5F0E5;">
                    <tr>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Payment Time</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Order #</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Transaction ID</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Cashier</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Status</th>
                        <th class="px-4 py-3 text-end" style="color:#4b3028; font-weight:700;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr style="border-bottom:1px solid #E9E6E2;">
                            <td class="px-4 py-3">
                                <div>{{ $payment->payment_time ? $payment->payment_time->format('d M Y') : '-' }}</div>
                                <div class="small text-muted">{{ $payment->payment_time ? $payment->payment_time->format('H:i') : '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="fw-bold" style="color:#A4823B;">#{{ $payment->order_id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <code class="small">{{ $payment->transaction_id }}</code>
                            </td>
                            <td class="px-4 py-3">
                                {{ $payment->user->username ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($payment->status === 'success')
                                    <span class="badge" style="background:#E6F9EE;color:#2D7A3B;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        <i class="bi bi-check-circle me-1"></i> Success
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge" style="background:#FFF4E6;color:#D68910;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        <i class="bi bi-clock-history me-1"></i> Pending
                                    </span>
                                @else
                                    <span class="badge" style="background:#FFECEC;color:#C0392B;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        <i class="bi bi-x-circle me-1"></i> Failed
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="fw-bold" style="color:#A4823B;">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size:2rem;color:#E9E6E2;"></i>
                                <div class="mt-2">No payment transactions found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-3 border-top" style="background:#FAFAFA;">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection