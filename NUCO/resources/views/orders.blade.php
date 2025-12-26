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
                                    @if($order->status === 'ready')
                                         <form method="POST" action="{{ route('orders.sent', $order) }}" class="d-inline" onsubmit="event.stopPropagation();">
                                             @csrf
                                             <button type="submit"
                                                     class="btn btn-sm order-action-btn"
                                                     style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 12px;font-weight:700;"
                                                     onclick="event.stopPropagation(); return confirm('Send order #{{ $order->id }} to the table?')">
                                                 Send
                                             </button>
                                         </form>
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
                                                        <div>
                                                            <div class="fw-semibold">{{ $product->name }}</div>
                                                            <div class="small text-muted">
                                                                x{{ $product->pivot->quantity }}
                                                                @if(!empty($product->pivot->note))
                                                                    • Note: {{ $product->pivot->note }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="text-nowrap">Rp {{ number_format($product->pivot->subtotal,0,',','.') }}</div>
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

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection