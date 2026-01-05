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

    <div class="checkout-grid">
        @forelse($orders as $order)
            <div class="checkout-card-wrapper">
                <div class="card shadow-sm border-0" style="border-radius:14px; overflow:hidden; height:100%; display:flex; flex-direction:column;">
                    {{-- Card Header --}}
                    <div class="p-3" style="background:linear-gradient(135deg, #A4823B 0%, #8B6F32 100%); flex-shrink:0;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-white">
                                <h5 class="mb-1 fw-bold">Order #{{ $order->id }}</h5>
                                <div class="small opacity-75">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                            @if($order->table)
                                <span class="badge" style="background:#F5F0E5; color:#A4823B; padding:8px 14px; border-radius:8px; font-weight:700; font-size:0.9rem;">
                                    <i class="bi bi-table me-1"></i>
                                    Table {{ $order->table->table_number }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Customer Info --}}
                    <div class="px-3 pt-3 pb-2" style="flex-shrink:0;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:40px; height:40px; background:#F5F0E5; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-person-fill" style="color:#A4823B; font-size:1.2rem;"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Customer</div>
                                <div class="fw-bold">{{ $order->order_name ?? 'No Name' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ Product List (SCROLLABLE if too many items) --}}
                    <div class="px-3 pb-3" style="flex:1; overflow-y:auto; min-height:0;">
                        <div class="small text-muted fw-semibold mb-2">Order Items</div>
                        <div class="list-group list-group-flush">
                            @foreach($order->products as $product)
                                <div class="list-group-item border-0 px-0 py-2" style="background:transparent;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        {{-- Product Name & Quantity --}}
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold" style="font-size:0.95rem;">
                                                {{ $product->name }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $product->pivot->quantity }}x @ Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </div>
                                        </div>

                                        {{-- Subtotal --}}
                                        <div class="text-end">
                                            <div class="fw-bold" style="color:#A4823B;">
                                                Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Total Summary --}}
                    <div class="px-3 pb-3" style="flex-shrink:0;">
                        <div class="p-3" style="background:#F5F0E5; border-radius:10px;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Subtotal</span>
                                <span class="fw-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold" style="font-size:1.1rem;">Total</span>
                                <span class="fw-bold" style="color:#A4823B; font-size:1.3rem;">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Button --}}
                    <div class="p-3" style="background:#FAFAFA; border-top:1px solid #E9E6E2; flex-shrink:0;">
                        <button type="button" class="btn w-100" 
                                style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:12px; font-weight:700; box-shadow:0 4px 12px rgba(164,130,59,0.25);"
                                data-bs-toggle="modal" 
                                data-bs-target="#paymentModal{{ $order->id }}">
                            <i class="bi bi-credit-card me-2"></i>
                            Process Payment
                        </button>
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
                            <div class="mb-3">
                                <div class="small text-muted mb-1">Order #{{ $order->id }}</div>
                                <div class="fw-bold" style="color:#A4823B; font-size:1.5rem;">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </div>
                            </div>

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

<style>
.checkout-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.checkout-card-wrapper {
    display: flex;
}

.checkout-card-wrapper > .card {
    flex: 1;
    min-height: 550px;
}

@media (max-width: 991.98px) {
    .checkout-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
    
    .checkout-card-wrapper > .card {
        min-height: 520px;
    }
}

@media (max-width: 575.98px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
    
    .checkout-card-wrapper > .card {
        min-height: 480px;
    }
}

.checkout-card-wrapper .card > div:nth-child(3) {
    scrollbar-width: thin;
    scrollbar-color: rgba(164, 130, 59, 0.3) transparent;
}

.checkout-card-wrapper .card > div:nth-child(3)::-webkit-scrollbar {
    width: 6px;
}

.checkout-card-wrapper .card > div:nth-child(3)::-webkit-scrollbar-track {
    background: transparent;
}

.checkout-card-wrapper .card > div:nth-child(3)::-webkit-scrollbar-thumb {
    background-color: rgba(164, 130, 59, 0.3);
    border-radius: 10px;
}
</style>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const discountSelect = document.querySelector(`#discount${orderId}`);
            const discountId = discountSelect ? discountSelect.value : '';

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

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
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            
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
                                    snap_token: data.snap_token,
                                    discount_id: discountId || null
                                })
                            })
                            .then(response => response.json())
                            .then(storeData => {
                                if (storeData.success) {
                                    alert('Payment successful!');
                                    window.location.reload();
                                } else {
                                    console.error('Store payment failed:', storeData);
                                    alert('Payment recorded but there was an issue: ' + (storeData.message || 'Unknown error'));
                                    window.location.reload();
                                }
                            })
                            .catch(error => {
                                console.error('Store payment error:', error);
                                alert('Payment success but failed to record! Please contact admin.');
                                setTimeout(() => {
                                    window.location.href = '{{ route("cashier.order.history") }}';
                                }, 2000);
                            });
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            alert('Payment pending. Please complete payment.');
                            btn.disabled = false;
                            btn.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pay with Midtrans';
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            alert('Payment failed: ' + (result.status_message || 'Unknown error'));
                            btn.disabled = false;
                            btn.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pay with Midtrans';
                        },
                        onClose: function() {
                            console.log('Popup closed without payment');
                            btn.disabled = false;
                            btn.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pay with Midtrans';
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
                alert('An error occurred while processing payment');
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-credit-card me-2"></i>Pay with Midtrans';
            });
        });
    });
});
</script>
@endsection