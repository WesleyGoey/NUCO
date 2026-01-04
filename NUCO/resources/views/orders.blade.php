@extends('layouts.mainlayout')

@section('title', 'Orders')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        @php $roleLabel = optional(auth()->user())->role ? ucfirst(auth()->user()->role) : 'Role'; @endphp
        <h3 class="mb-0 fw-bold" style="color:#4b3028;">Orders ({{ $roleLabel }})</h3>
        <small class="text-muted">Showing {{ $orders->total() }} orders</small>
    </div>



    @php
        $statuses = ['all' => 'All', 'pending' => 'Pending', 'processing' => 'Processing', 'ready' => 'Ready', 'sent' => 'Sent', 'completed' => 'Completed'];
    @endphp

    {{-- Filter pills like menu.blade.php --}}
    <div class="mb-3 overflow-auto" style="white-space:nowrap; -webkit-overflow-scrolling:touch;">
        @foreach($statuses as $key => $label)
            @php $active = ($filter ?? 'all') === $key; @endphp
            {{-- always include status param so "All" becomes ?status=all --}}
            <a href="{{ route('orders', ['status' => $key]) }}"
               class="btn btn-sm me-2 mb-2"
               style="{{ $active ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                {{ $label }}
                @if(isset($counts[$key]))
                    <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">
                        {{ $counts[$key] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead style="background:#F5F0E5;">
                        <tr>
                            <th>Order #</th>
                            <th>Date & Time</th>
                            <th>Table</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#order-details-{{ $order->id }}" aria-expanded="false">
                                <td class="px-3">#{{ $order->id }}</td>
                                <td class="px-3">
                                    {{ $order->created_at->format('d M Y') }}<br>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td class="px-3">
                                    @if($order->table)
                                        <span class="badge" style="background:#F5F0E5;color:#A4823B;padding:6px 10px;border-radius:8px;font-weight:700;">
                                            Table {{ $order->table->table_number }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-3">{{ $order->order_name ?? ($order->user->username ?? '-') }}</td>
                                <td class="px-3"><span class="badge bg-secondary">{{ $order->products->sum('pivot.quantity') }} items</span></td>
                                <td class="px-3">
                                    <span class="fw-semibold"
                                          style="color: {{ $order->status === 'completed' ? '#2D7A3B' : ($order->status === 'processing' ? '#D68910' : ($order->status === 'sent' ? '#1f6feb' : '#6b6b6b')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-3 text-end">
                                    @php
                                        $user = auth()->user();
                                        $isOwner = $user && method_exists($user, 'isOwner') && $user->isOwner();
                                        $isChef = $user && method_exists($user, 'isChef') && $user->isChef();
                                        $isWaiter = $user && method_exists($user, 'isWaiter') && $user->isWaiter();
                                        $hasAction = false;
                                    @endphp

                                    {{-- Chef Actions --}}
                                    @if($isChef)
                                        @if($order->status === 'pending')
                                            @php $hasAction = true; @endphp
                                            <form method="POST" action="{{ route('orders.processing', $order) }}" class="d-inline" onsubmit="event.stopPropagation();">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm order-action-btn"
                                                        style="background:#D68910;color:#F5F0E5;border:none;border-radius:8px;padding:6px 12px;font-weight:700;"
                                                        onclick="event.stopPropagation(); return confirm('Start processing order #{{ $order->id }}?')">
                                                    <i class="bi bi-arrow-right-circle me-1"></i>
                                                    Process
                                                </button>
                                            </form>
                                        @elseif($order->status === 'processing')
                                            @php $hasAction = true; @endphp
                                            <form method="POST" action="{{ route('orders.ready', $order) }}" class="d-inline" onsubmit="event.stopPropagation();">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm order-action-btn"
                                                        style="background:#2D7A3B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 12px;font-weight:700;"
                                                        onclick="event.stopPropagation(); return confirm('Mark order #{{ $order->id }} as ready?')">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Ready
                                                </button>
                                            </form>
                                        @endif

                                    {{-- Waiter Actions --}}
                                    @elseif($isWaiter && $order->status === 'ready')
                                        @php $hasAction = true; @endphp
                                        <form method="POST" action="{{ route('orders.sent', $order) }}" class="d-inline" onsubmit="event.stopPropagation();">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm order-action-btn"
                                                    style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 12px;font-weight:700;"
                                                    onclick="event.stopPropagation(); return confirm('Send order #{{ $order->id }} to the table?')">
                                                <i class="bi bi-send me-1"></i>
                                                Send
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Show "No action" if nothing was rendered --}}
                                    @if(!$hasAction)
                                        <span class="text-muted small">
                                            <i class="bi bi-dash-circle me-1"></i>
                                            No action
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            {{-- expandable details row --}}
                            <tr class="collapse" id="order-details-{{ $order->id }}">
                                <td colspan="7" style="background:#FAFAFA;">
                                    <div class="row py-3">
                                        <div class="col-12 col-md-8">
                                            <div class="fw-semibold mb-2">Items</div>
                                            <ul class="list-unstyled mb-0">
                                                @foreach($order->products as $product)
                                                    <li class="mb-2 d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold">{{ $product->name }}</div>
                                                            <div class="small text-muted">
                                                                Quantity: {{ $product->pivot->quantity }}x
                                                            </div>
                                                            {{-- ✅ FIXED: Show notes if exists --}}
                                                            @if(!empty($product->pivot->note))
                                                                <div class="small mt-1" style="color:#A4823B; background:#FFF9E6; padding:4px 8px; border-radius:6px; display:inline-block;">
                                                                    <i class="bi bi-chat-left-text me-1"></i>
                                                                    <strong>Note:</strong> {{ $product->pivot->note }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="text-nowrap ms-3">
                                                            Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="fw-semibold mb-2">Total</div>
                                            <div class="fw-bold" style="color:#A4823B;">
                                                Rp {{ number_format($order->total_price,0,',','.') }}
                                            </div>
                                            <div class="mt-3">
                                                <small class="text-muted">Order created: {{ $order->created_at->format('d M Y, H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size:2.5rem; color:#D0D0D0;"></i>
                                    <div class="mt-3">No orders found.</div>
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
                            <a class="page-link" href="{{ $orders->appends(['status' => request('status')])->previousPageUrl() }}" rel="prev" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">&laquo;</a>
                        </li>
                    @endif

                    @foreach ($orders->links()->elements[0] as $page => $url)
                        @if ($page == $orders->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="color:#F5F0E5; background:#A4823B; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:700; display:inline-block; box-shadow:0 4px 12px rgba(164,130,59,0.3);">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $orders->appends(['status' => request('status')])->url($page) }}" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if ($orders->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->appends(['status' => request('status')])->nextPageUrl() }}" rel="next" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">&raquo;</a>
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
@endsection