@extends('layouts.mainlayout')

@section('title', 'Checkout - Unpaid Orders')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold" style="color:#4b3028;">Checkout</h3>
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

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius:10px;">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius:10px;">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        @forelse($orders as $order)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0" style="border-radius:14px; overflow:hidden;">
                    <div class="card-body p-3">
                        {{-- Header --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1 fw-bold" style="color:#A4823B;">Order #{{ $order->id }}</h5>
                                <div class="small text-muted">{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </div>
                            @if($order->table)
                                <span class="badge" style="background:#F5F0E5; color:#A4823B; padding:6px 12px; border-radius:8px; font-weight:700;">
                                    Table {{ $order->table->table_number }}
                                </span>
                            @endif
                        </div>

                        {{-- Customer (using order_name) --}}
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Customer</div>
                            {{-- ✅ CHANGED: Display order_name only --}}
                            <div class="fw-semibold">{{ $order->order_name ?? 'No Name' }}</div>
                        </div>

                        {{-- Items --}}
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Items</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($order->products as $product)
                                    <span class="badge bg-secondary">{{ $product->name }} ({{ $product->pivot->quantity }}x)</span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total</span>
                            <span class="fw-bold" style="color:#A4823B; font-size:1.25rem;">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Action Button (only Process Payment, no Cancel) --}}
                        <div class="d-grid">
                            {{-- Payment Button --}}
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

            {{-- Payment Modal --}}
            <div class="modal fade" id="paymentModal{{ $order->id }}" tabindex="-1" aria-labelledby="paymentModalLabel{{ $order->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius:14px; border:none;">
                        <div class="modal-header" style="background:#A4823B; color:#F5F0E5; border-radius:14px 14px 0 0;">
                            <h5 class="modal-title fw-bold" id="paymentModalLabel{{ $order->id }}">Process Payment</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            {{-- Order Summary --}}
                            <div class="mb-3">
                                <div class="small text-muted mb-1">Order #{{ $order->id }}</div>
                                <div class="fw-bold" style="color:#A4823B; font-size:1.5rem;">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </div>
                            </div>

                            {{-- Discount Selection --}}
                            <div class="mb-3">
                                <label for="discount{{ $order->id }}" class="form-label fw-bold" style="color:#4b3028;">Apply Discount (Optional)</label>
                                <select name="discount_id" id="discount{{ $order->id }}" class="form-select discount-select" style="border-radius:10px; padding:10px;" data-order-id="{{ $order->id }}" data-order-total="{{ $order->total_price }}">
                                    <option value="">No Discount</option>
                                    @foreach($activeDiscounts as $disc)
                                        <option value="{{ $disc->id }}" 
                                                data-type="{{ $disc->type }}" 
                                                data-value="{{ $disc->value }}"
                                                data-min="{{ $disc->min_order_amount ?? 0 }}">
                                            {{ $disc->name }} 
                                            @if($disc->type === 'percent')
                                                ({{ $disc->value }}%)
                                            @else
                                                (Rp {{ number_format($disc->value, 0, ',', '.') }})
                                            @endif
                                            @if($disc->min_order_amount)
                                                - Min: Rp {{ number_format($disc->min_order_amount, 0, ',', '.') }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Price Breakdown --}}
                            <div class="card" style="background:#F9F9F9; border:1px solid #E9E6E2; border-radius:10px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span class="fw-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2" id="discount-row-{{ $order->id }}" style="display:none !important;">
                                        <span class="text-success">Discount</span>
                                        <span class="text-success fw-semibold" id="discount-amount-{{ $order->id }}">- Rp 0</span>
                                    </div>
                                    <hr style="margin:8px 0;">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">Total</span>
                                        <span class="fw-bold" style="color:#A4823B; font-size:1.25rem;" id="final-total-{{ $order->id }}">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="button" class="btn w-100 mt-3 pay-btn" 
                                    data-order-id="{{ $order->id }}"
                                    style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:12px; font-weight:700;">
                                <i class="bi bi-credit-card me-2"></i>
                                Pay with Midtrans
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ REMOVED: Cancel Modal dihapus --}}

        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius:14px;">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size:3rem; color:#D0D0D0;"></i>
                        <div class="mt-3 text-muted">No unpaid orders at the moment.</div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- ✅ Midtrans Snap Script --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Discount selection real-time calculation
    document.querySelectorAll('.discount-select').forEach(select => {
        select.addEventListener('change', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderTotal = parseFloat(this.getAttribute('data-order-total'));
            const selectedOption = this.options[this.selectedIndex];
            
            const discountRow = document.getElementById(`discount-row-${orderId}`);
            const discountAmount = document.getElementById(`discount-amount-${orderId}`);
            const finalTotal = document.getElementById(`final-total-${orderId}`);
            
            if (this.value) {
                const discountType = selectedOption.getAttribute('data-type');
                const discountValue = parseFloat(selectedOption.getAttribute('data-value'));
                const minAmount = parseFloat(selectedOption.getAttribute('data-min') || 0);
                
                if (minAmount > 0 && orderTotal < minAmount) {
                    alert(`Minimum order amount is Rp ${minAmount.toLocaleString('id-ID')}`);
                    this.value = '';
                    discountRow.style.display = 'none';
                    finalTotal.textContent = `Rp ${orderTotal.toLocaleString('id-ID')}`;
                    return;
                }
                
                let discount = 0;
                if (discountType === 'percent') {
                    discount = (orderTotal * discountValue) / 100;
                } else {
                    discount = discountValue;
                }
                
                const final = orderTotal - discount;
                
                discountRow.style.display = 'flex';
                discountAmount.textContent = `- Rp ${discount.toLocaleString('id-ID')}`;
                finalTotal.textContent = `Rp ${final.toLocaleString('id-ID')}`;
            } else {
                discountRow.style.display = 'none';
                finalTotal.textContent = `Rp ${orderTotal.toLocaleString('id-ID')}`;
            }
        });
    });

    // ✅ Handle payment button click
    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const discountSelect = document.querySelector(`#discount${orderId}`);
            const discountId = discountSelect ? discountSelect.value : '';

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            // ✅ Step 1: Get Snap Token dari backend
            fetch('{{ route("cashier.payment.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    discount_id: discountId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ✅ Step 2: Open Midtrans Snap popup
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);

                            // ✅ Step 3: Store payment record ke database
                            fetch('{{ route("cashier.payment.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    order_id: data.order_id,
                                    transaction_id: data.transaction_id,
                                    amount: data.final_amount,
                                    cashier_id: data.cashier_id,
                                    snap_token: data.snap_token
                                })
                            })
                            .then(response => response.json())
                            .then(paymentData => {
                                if (paymentData.success) {
                                    alert('Payment success!');
                                    window.location.reload();
                                } else {
                                    alert('Payment recorded but failed to save: ' + paymentData.message);
                                    window.location.reload();
                                }
                            })
                            .catch(error => {
                                console.error('Store payment error:', error);
                                alert('Payment success but failed to record!');
                                window.location.reload();
                            });
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            // ✅ JANGAN STORE PAYMENT, hanya log
                            alert('Payment pending. Please complete payment.');
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            alert('Payment failed!');
                            window.location.reload();
                        },
                        onClose: function() {
                            console.log('Popup closed without payment');
                            // ✅ JANGAN STORE PAYMENT, user cancel
                            // Re-enable button
                            const btn = document.querySelector(`[data-order-id="${orderId}"]`);
                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pay with Midtrans';
                            }
                        }
                    });
                } else {
                    alert(data.message || 'Failed to process payment');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pay with Midtrans';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pay with Midtrans';
            });
        });
    });
});
</script>
@endsection