@extends('layouts.mainlayout')

@section('title', 'Order History')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Order History</h3>
            <div class="small text-muted">All paid orders</div>
        </div>
        <a href="{{ route('cashier.checkout') }}" class="btn" style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:10px 20px; font-weight:700;">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Checkout
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius:14px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#F5F0E5;">
                        <tr>
                            <th class="px-4 py-3">Order #</th>
                            <th class="px-4 py-3">Date & Time</th>
                            <th class="px-4 py-3">Table</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Items</th>
                            <th class="px-4 py-3 text-end">Total Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#order-details-{{ $order->id }}">
                                <td class="px-4 py-3">
                                    <span class="fw-bold" style="color:#A4823B;">#{{ $order->id }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div>{{ $order->payment->payment_time->format('d M Y') }}</div>
                                    <div class="small text-muted">{{ $order->payment->payment_time->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($order->table)
                                        <span class="badge" style="background:#F5F0E5; color:#A4823B; padding:6px 12px; border-radius:8px; font-weight:700;">
                                            Table {{ $order->table->table_number }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    {{ $order->order_name ?? 'No Name' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-secondary">{{ $order->products->count() }} items</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <span class="fw-bold" style="color:#A4823B;">
                                        Rp {{ number_format($order->payment->amount, 0, ',', '.') }}
                                    </span>
                                    
                                    @if($order->discount)
                                        <div class="small text-muted text-decoration-line-through">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="collapse" id="order-details-{{ $order->id }}">
                                <td colspan="7" class="px-4 py-3" style="background:#FAFAFA;">
                                    <div class="ms-4">
                                        <div class="fw-semibold mb-2">Order Items:</div>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($order->products as $product)
                                                <li class="mb-1">
                                                    <span class="text-muted">•</span>
                                                    {{ $product->name }} 
                                                    <span class="text-muted">x{{ $product->pivot->quantity }}</span>
                                                    <span class="ms-2">Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}</span>
                                                    @if($product->pivot->note)
                                                        <div class="small text-muted ms-3">Note: {{ $product->pivot->note }}</div>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if($order->discount)
                                            <div class="mt-2 p-2" style="background:#FFF9E6; border-radius:8px; display:inline-block;">
                                                <i class="bi bi-tag-fill me-2" style="color:#D68910;"></i>
                                                <span class="fw-semibold">{{ $order->discount->name }}</span>
                                                <span class="text-muted ms-2">
                                                    @if($order->discount->type === 'percent')
                                                        ({{ $order->discount->value }}% off)
                                                    @else
                                                        (Rp {{ number_format($order->discount->value, 0, ',', '.') }} off)
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <div class="mt-2">
                                                <div class="small">
                                                    <span class="text-muted">Subtotal:</span>
                                                    <span class="fw-semibold ms-2">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="small text-success">
                                                    <span>Discount:</span>
                                                    <span class="fw-semibold ms-2">
                                                        - Rp {{ number_format($order->total_price - $order->payment->amount, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                <hr class="my-1">
                                                <div class="small fw-bold">
                                                    <span>Total Paid:</span>
                                                    <span class="ms-2" style="color:#A4823B;">
                                                        Rp {{ number_format($order->payment->amount, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size:3rem; color:#D0D0D0;"></i>
                                    <p class="text-muted mt-3">No payment history found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ✅ CUSTOM INLINE PAGINATION --}}
    @if ($orders->lastPage() > 1)
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination" style="gap:0.7rem; margin:0; padding:0; list-style:none; display:flex;">
                    @if ($orders->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; cursor:not-allowed; display:inline-block;">&laquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">&laquo;</a>
                        </li>
                    @endif

                    @foreach ($orders->links()->elements[0] as $page => $url)
                        @if ($page == $orders->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="color:#F5F0E5; background:#A4823B; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:700; display:inline-block; box-shadow:0 4px 12px rgba(164,130,59,0.3);">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if ($orders->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">&raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; cursor:not-allowed; display:inline-block;">&raquo;</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
</div>

<style>
tbody tr[data-bs-toggle]:hover {
    background-color: #F9F9F9;
}

.pagination {
    gap: 0.5rem;
}

.pagination .page-link {
    color: #A4823B;
    border: 1px solid #E9E6E2;
    border-radius: 8px;
}

.pagination .page-link:hover {
    background-color: #F5F0E5;
    color: #A4823B;
}

.pagination .page-item.active .page-link {
    background-color: #A4823B;
    border-color: #A4823B;
    color: #F5F0E5;
}
</style>
@endsection