@extends('layouts.mainlayout')

@section('title', 'Checkout - Unpaid Orders')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Checkout</h3>
            <div class="small text-muted">Process payments for unpaid orders</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="small text-muted">Total Unpaid Orders</div>
                <div class="fw-bold" style="color:#A4823B; font-size:1.5rem;">{{ $orders->count() }}</div>
            </div>
            <a href="{{ route('cashier.order.history') }}" class="btn btn-outline-secondary" style="border-radius:10px; padding:10px 20px; font-weight:600; border-color:#A4823B; color:#A4823B;">
                <i class="bi bi-clock-history me-2"></i>
                Order History
            </a>
        </div>
    </div>

    <div class="row g-3">
        @forelse($orders as $order)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0" style="border-radius:14px; overflow:hidden;">
                    <div class="card-body d-flex flex-column">
                        <!-- Order Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Order #{{ $order->id }}</h5>
                                <div class="small text-muted">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                            <span class="badge" style="background: {{ $order->status === 'completed' ? '#E6F9EE' : '#FFF4E6' }}; color: {{ $order->status === 'completed' ? '#2D7A3B' : '#D68910' }}; padding:6px 12px; border-radius:8px; font-weight:700;">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Table & Customer Info -->
                        <div class="mb-3 pb-3 border-bottom">
                            @if($order->table)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-table me-2" style="color:#A4823B;"></i>
                                    <span class="fw-semibold">Table {{ $order->table->table_number }}</span>
                                </div>
                            @endif
                            @if($order->order_name || $order->user)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person me-2" style="color:#A4823B;"></i>
                                    <span class="small">{{ $order->order_name ?? ($order->user->username ?? '-') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Order Items -->
                        <div class="mb-3">
                            <div class="fw-semibold mb-2 small text-muted">ITEMS ({{ $order->products->count() }})</div>
                            <div class="small" style="max-height:120px; overflow-y:auto;">
                                @foreach($order->products as $product)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-truncate me-2">{{ $product->name }} x{{ $product->pivot->quantity }}</span>
                                        <span class="text-nowrap">Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Discount Info (if already applied) -->
                        @if($order->discount)
                            <div class="mb-3 p-2" style="background:#FFF9E6; border-radius:8px;">
                                <div class="small d-flex align-items-center">
                                    <i class="bi bi-tag-fill me-2" style="color:#D68910;"></i>
                                    <span class="fw-semibold">{{ $order->discount->name }}</span>
                                </div>
                                <div class="small text-muted ms-4">
                                    @if($order->discount->type === 'percent')
                                        {{ $order->discount->value }}% off
                                    @else
                                        Rp {{ number_format($order->discount->value, 0, ',', '.') }} off
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Total Amount -->
                        <div class="mt-auto pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold">Total Amount</span>
                                <span class="fw-bold" style="color:#A4823B; font-size:1.25rem;">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Payment Button -->
                            <button type="button" class="btn w-100" 
                                    style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:12px; font-weight:700;"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#paymentModal{{ $order->id }}">
                                <i class="bi bi-credit-card me-2"></i>
                                Process Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Modal -->
            <div class="modal fade" id="paymentModal{{ $order->id }}" tabindex="-1" aria-labelledby="paymentModalLabel{{ $order->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius:14px; border:none;">
                        <div class="modal-header" style="background:#A4823B; color:#F5F0E5; border-radius:14px 14px 0 0;">
                            <h5 class="modal-title fw-bold" id="paymentModalLabel{{ $order->id }}">Process Payment</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('cashier.payment.process') }}" id="paymentForm{{ $order->id }}">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="amount" id="finalAmount{{ $order->id }}" value="{{ $order->total_price }}">
                            
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <div class="small text-muted mb-1">Order #{{ $order->id }}</div>
                                    <div class="fw-bold" style="font-size:1.5rem; color:#A4823B;">
                                        Rp <span id="displayAmount{{ $order->id }}">{{ number_format($order->total_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                {{-- ✅ NEW: Discount Selection --}}
                                @if($activeDiscounts->isNotEmpty())
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Apply Discount (Optional)</label>
                                        <select name="discount_id" class="form-select" id="discountSelect{{ $order->id }}" 
                                                style="border-radius:10px; padding:10px;"
                                                onchange="updateTotal{{ $order->id }}()">
                                            <option value="">No Discount</option>
                                            @foreach($activeDiscounts as $discount)
                                                @php
                                                    // Check if order meets minimum requirement
                                                    $canApply = !$discount->min_order_amount || $order->total_price >= $discount->min_order_amount;
                                                @endphp
                                                <option value="{{ $discount->id }}" 
                                                        data-type="{{ $discount->type }}"
                                                        data-value="{{ $discount->value }}"
                                                        {{ !$canApply ? 'disabled' : '' }}
                                                        {{ $order->discount_id == $discount->id ? 'selected' : '' }}>
                                                    {{ $discount->name }} 
                                                    ({{ $discount->type === 'percent' ? $discount->value . '%' : 'Rp ' . number_format($discount->value, 0, ',', '.') }})
                                                    @if($discount->min_order_amount)
                                                        - Min Rp {{ number_format($discount->min_order_amount, 0, ',', '.') }}
                                                    @endif
                                                    @if(!$canApply)
                                                        (Not eligible)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="small text-muted mt-1">Select a discount to reduce the total amount</div>
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Payment Method</label>
                                    <div class="d-grid gap-2">
                                        <div class="form-check p-3" style="background:#F5F0E5; border-radius:10px; cursor:pointer;">
                                            <input class="form-check-input" type="radio" name="method" id="qris{{ $order->id }}" value="qris" required>
                                            <label class="form-check-label w-100" for="qris{{ $order->id }}" style="cursor:pointer;">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-qr-code fs-4 me-3" style="color:#A4823B;"></i>
                                                    <div>
                                                        <div class="fw-semibold">QRIS</div>
                                                        <div class="small text-muted">Scan QR Code</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="form-check p-3" style="background:#F5F0E5; border-radius:10px; cursor:pointer;">
                                            <input class="form-check-input" type="radio" name="method" id="cash{{ $order->id }}" value="cash" required>
                                            <label class="form-check-label w-100" for="cash{{ $order->id }}" style="cursor:pointer;">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-cash-stack fs-4 me-3" style="color:#A4823B;"></i>
                                                    <div>
                                                        <div class="fw-semibold">Cash</div>
                                                        <div class="small text-muted">Pay with physical money</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px; padding:10px 20px;">Cancel</button>
                                <button type="submit" class="btn" style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:10px 24px; font-weight:700;">
                                    Confirm Payment
                                </button>
                            </div>
                        </form>

                        {{-- ✅ JavaScript to calculate discount in real-time --}}
                        <script>
                        function updateTotal{{ $order->id }}() {
                            const select = document.getElementById('discountSelect{{ $order->id }}');
                            const originalTotal = {{ $order->total_price }};
                            const displayAmount = document.getElementById('displayAmount{{ $order->id }}');
                            const finalAmountInput = document.getElementById('finalAmount{{ $order->id }}');

                            if (!select || !displayAmount || !finalAmountInput) return;

                            const selectedOption = select.options[select.selectedIndex];
                            
                            if (!selectedOption || !selectedOption.value) {
                                // No discount selected
                                displayAmount.textContent = originalTotal.toLocaleString('id-ID');
                                finalAmountInput.value = originalTotal;
                                return;
                            }

                            const discountType = selectedOption.getAttribute('data-type');
                            const discountValue = parseFloat(selectedOption.getAttribute('data-value'));
                            
                            let discountAmount = 0;
                            if (discountType === 'percent') {
                                discountAmount = (originalTotal * discountValue) / 100;
                            } else {
                                discountAmount = discountValue;
                            }

                            const finalTotal = Math.max(0, originalTotal - discountAmount);
                            
                            displayAmount.textContent = finalTotal.toLocaleString('id-ID');
                            finalAmountInput.value = finalTotal;
                        }

                        // Initialize on page load (if discount already applied)
                        document.addEventListener('DOMContentLoaded', function() {
                            updateTotal{{ $order->id }}();
                        });
                        </script>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius:14px;">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-check-circle" style="font-size:4rem; color:#2D7A3B;"></i>
                        <h5 class="mt-3 mb-2">All Orders Paid!</h5>
                        <p class="text-muted">There are no unpaid orders at the moment.</p>
                        <a href="{{ route('cashier.order.history') }}" class="btn" style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:10px 20px; font-weight:600;">
                            View Order History
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
/* Enhance radio button hover effect */
.form-check:has(.form-check-input:checked) {
    background: #A4823B !important;
}

.form-check:has(.form-check-input:checked) label,
.form-check:has(.form-check-input:checked) .text-muted {
    color: #F5F0E5 !important;
}

.form-check:has(.form-check-input:checked) i {
    color: #F5F0E5 !important;
}

.form-check:hover {
    background: #E9E6E2 !important;
}
</style>
@endsection