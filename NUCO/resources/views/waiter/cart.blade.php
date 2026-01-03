<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu — Order (Cart) - {{ config('app.name', 'NUCO') }}</title>

    <!-- Bootstrap / Icons / App CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
</head>
<body style="background:#F5F0E5; min-height:100vh; margin:0;">

@includeIf('layouts.navigation-waiter')

@php
    $selectedTable = session('selected_table') ?? null;
    $cart = session('cart', []);
    $cartCount = array_sum(array_map(fn($i)=>(int)($i['quantity'] ?? 0), $cart ?: []));
    $cartTotal = array_sum(array_map(fn($i)=>(int)($i['subtotal'] ?? 0), $cart ?: []));
@endphp

<div class="container-xl py-4">
   {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="m-0 fw-bold">Menu & Cart</h4>
            <div class="text-muted small">
                @if($selectedTable)
                    Table {{ $selectedTable['table_number'] }} • {{ $cartCount }} items in cart
                @else
                    No table selected
                @endif
            </div>
        </div>
    </div>

    {{-- ✅ FIXED: Search & Filter sejajar TANPA card background --}}
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            
            {{-- Category Filters (kiri, scrollable) --}}
            <div class="overflow-auto" style="white-space:nowrap; -webkit-overflow-scrolling:touch; flex:1;">
                @php $allActive = empty($selectedCategoryId); @endphp
                <a href="{{ route('waiter.cart', ['search' => $search ?? '']) }}"
                   class="btn btn-sm me-2 mb-2"
                   style="{{ $allActive ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                    All
                    <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">
                        {{ $totalProductsCount }}
                    </span>
                </a>

                @foreach($allCategoriesForButtons as $cat)
                    @php
                        $catId = (string) $cat->id;
                        $active = !empty($selectedCategoryId) && (string)$selectedCategoryId === $catId;
                        $productCount = $cat->products()->where('is_available', 1)->count();
                    @endphp
                    <a href="{{ route('waiter.cart', ['category' => $catId, 'search' => $search ?? '']) }}"
                       class="btn btn-sm me-2 mb-2"
                       style="{{ $active ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                        {{ $cat->name }}
                        <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">
                            {{ $productCount }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Search Box (kanan) --}}
            <div style="min-width:260px;">
                <form method="GET" action="{{ route('waiter.cart') }}" class="d-flex">
                    @if(!empty($selectedCategoryId))
                        <input type="hidden" name="category" value="{{ $selectedCategoryId }}">
                    @endif
                    <input name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                           placeholder="Search menu..." 
                           style="border-radius:10px;border:1px solid #E9E6E2;padding:8px;background:#ffffff;" />
                    <button type="submit" class="btn btn-sm ms-2"
                            style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 12px;font-weight:600;">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Content: Menu (2/3) + Orders (1/3) --}}
    <div class="row g-3">
        {{-- LEFT: Menu Products (2/3) --}}
        <div class="col-12 col-lg-8">
            @php
                $totalDisplayed = $categories->sum(fn($c) => $c->products->count());
            @endphp

            @if ($totalDisplayed === 0)
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:3rem;"></i>
                    <p class="mt-2">
                        @if(!empty($search))
                            No results found for "{{ e($search) }}"
                        @else
                            No products available
                        @endif
                    </p>
                </div>
            @else
                @foreach($categories as $category)
                    @if($category->products->isEmpty())
                        @continue
                    @endif

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color:#4b3028;">{{ $category->name }}</h5>
                        <div class="row g-3">
                            @foreach($category->products as $product)
                                <div class="col-12 col-sm-6 col-md-4">
                                    {{-- Product Card --}}
                                    <div class="card h-100 shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                                        <div style="border-radius:12px 12px 0 0; overflow:hidden;">
                                            <div class="ratio ratio-4x3">
                                                @if(!empty($product->image_path))
                                                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                                                         alt="{{ $product->name }}" 
                                                         style="width:100%; height:100%; object-fit:cover; display:block;">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center w-100 h-100"
                                                         style="background:#FFFFFF; border:1px dashed rgba(0,0,0,0.06);"></div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title fw-bold mb-1 text-truncate">{{ $product->name }}</h5>
                                            @if(!empty($product->description))
                                                <p class="card-text text-muted small mb-2" style="line-height:1.3;">{{ $product->description }}</p>
                                            @endif

                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <div class="fw-bold" style="color:#A4823B;">
                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </div>

                                                {{-- ✅ Check stock availability --}}
                                                @if(!empty($product->is_available) && $product->is_available && ($product->in_stock ?? true))
                                                    <form method="POST" action="{{ route('waiter.cart.add') }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="btn btn-sm"
                                                                style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 10px;font-weight:600;">
                                                            Add
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>Out of stock</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- RIGHT: Orders (1/3) --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top:20px; border-radius:12px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Orders</h5>
                    <div class="small text-muted mb-3">
                        Table {{ $selectedTable['table_number'] ?? '-' }} • {{ $cartCount }} items
                    </div>

                    <hr>

                    {{-- Cart Items --}}
                    <div class="overflow-auto" style="max-height:500px;">
                        @if(empty($cart))
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-cart" style="font-size:2rem;"></i>
                                <p class="mt-2 small">Cart is empty</p>
                            </div>
                        @else
                            <div class="list-group mb-3">
                                @foreach($cart as $item)
                                    <div class="list-group-item border-0 p-3 mb-2" style="background:#F9F9F9; border-radius:8px;">
                                        {{-- Product Name & Price --}}
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1 me-2">
                                                <div class="fw-bold mb-1">{{ $item['name'] }}</div>
                                                <div class="small text-muted">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }} × {{ $item['quantity'] ?? 1 }}</div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold" style="color:#A4823B;">
                                                    Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Notes Input --}}
                                        <div class="mb-2">
                                            <form method="POST" action="{{ route('waiter.cart.update-note') }}" class="d-flex gap-2">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                <input type="text" name="notes"
                                                       value="{{ $item['notes'] ?? '' }}" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Add note (e.g., no spicy)..."
                                                       style="border-radius:6px; font-size:0.85rem; border:1px solid #E9E6E2;">
                                                <button type="submit" class="btn btn-sm" 
                                                        style="background:#A4823B; color:#F5F0E5; border-radius:6px; padding:4px 10px; border:none;">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        </div>

                                        {{-- Quantity Controls & Delete --}}
                                        <div class="d-flex align-items-center justify-content-between">
                                            {{-- Quantity Controls --}}
                                            <div class="d-flex align-items-center gap-2">
                                                <form method="POST" action="{{ route('waiter.cart.update') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="action" value="decrease">
                                                    <button type="submit" class="btn btn-sm" 
                                                            style="width:32px; height:32px; padding:0; border-radius:6px; border:1px solid #E9E6E2; background:#ffffff; display:flex; align-items:center; justify-content:center;"
                                                            {{ ($item['quantity'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                                        <i class="bi bi-dash" style="color:#6b6b6b;"></i>
                                                    </button>
                                                </form>

                                                <span class="fw-bold" style="min-width:32px; text-align:center; font-size:1rem;">{{ $item['quantity'] ?? 1 }}</span>

                                                <form method="POST" action="{{ route('waiter.cart.update') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="action" value="increase">
                                                    <button type="submit" class="btn btn-sm" 
                                                            style="width:32px; height:32px; padding:0; border-radius:6px; border:1px solid #E9E6E2; background:#ffffff; display:flex; align-items:center; justify-content:center;">
                                                        <i class="bi bi-plus" style="color:#6b6b6b;"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- Delete Button --}}
                                            <form method="POST" action="{{ route('waiter.cart.remove') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                <button type="submit" class="btn btn-sm" 
                                                        style="width:32px; height:32px; padding:0; border-radius:6px; border:1px solid #dc3545; background:#ffffff; display:flex; align-items:center; justify-content:center;"
                                                        onclick="return confirm('Remove this item from cart?')">
                                                    <i class="bi bi-trash" style="color:#dc3545;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Total Section --}}
                            <div class="p-3 mb-3" style="background:#F5F0E5; border-radius:8px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fw-bold" style="font-size:1.1rem; color:#4b3028;">Total</div>
                                    <div class="fw-bold" style="color:#A4823B; font-size:1.3rem;">
                                        Rp {{ number_format($cartTotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            @if(!empty($cart))
                                {{-- Checkout Button - Open Modal --}}
                                <button type="button" class="btn w-100 d-flex align-items-center justify-content-center gap-2"
                                        data-bs-toggle="modal" data-bs-target="#checkoutModal"
                                        style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:12px; font-weight:700; box-shadow:0 4px 12px rgba(164,130,59,0.25);">
                                    <i class="bi bi-cart-check"></i>
                                    <span>Checkout</span>
                                </button>
                            @else
                                <button type="button" class="btn w-100" disabled
                                        style="background:#E9E6E2; color:#A1A09A; border:none; border-radius:10px; padding:12px; font-weight:700;">
                                    <i class="bi bi-cart-x me-2"></i>
                                    Cart is Empty
                                </button>
                            @endif

                            {{-- ✅ UPDATED: Cancel Order Button (Thin Red Border like Delete Button) --}}
                            @if($selectedTable)
                                <form method="POST" action="{{ route('waiter.tables.cancel') }}">
                                    @csrf
                                    <button type="submit" class="btn w-100"
                                            onclick="return confirm('Are you sure you want to cancel this order?')"
                                            style="background:#ffffff; color:#dc3545; border:1px solid #dc3545; border-radius:10px; padding:10px; font-weight:600;">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ UPDATED: Checkout Modal (Removed Info Alert) --}}
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header" style="background:#A4823B; color:#F5F0E5; border:none;">
                <h5 class="modal-title fw-bold" id="checkoutModalLabel">
                    <i class="bi bi-person-badge me-2"></i>
                    Customer Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('waiter.cart.checkout') }}">
                @csrf
                <div class="modal-body p-4">
                    {{-- Table Info --}}
                    @if($selectedTable)
                        <div class="mb-3 p-3" style="background:#F5F0E5; border-radius:10px;">
                            <div class="small text-muted mb-1">Table Number</div>
                            <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">
                                {{ $selectedTable['table_number'] }}
                            </div>
                        </div>
                    @endif

                    {{-- Cart Summary --}}
                    <div class="mb-3 p-3" style="background:#F9F9F9; border-radius:10px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Items</span>
                            <span class="fw-semibold">{{ $cartCount }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total Price</span>
                            <span class="fw-bold" style="color:#A4823B; font-size:1.25rem;">
                                Rp {{ number_format($cartTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Customer Name Input --}}
                    <div class="mb-0">
                        <label for="customer_name" class="form-label fw-bold" style="color:#4b3028;">
                            Customer Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" 
                               name="customer_name" 
                               placeholder="Enter customer name"
                               value="{{ old('customer_name') }}"
                               required
                               style="border-radius:10px; border:1px solid #E9E6E2; padding:12px;">
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer" style="border:none; padding:16px 24px;">
                    {{-- ✅ UPDATED: Cancel button (Thin Red Border) --}}
                    <button type="button" class="btn" data-bs-dismiss="modal" 
                            style="background:#ffffff; color:#dc3545; border:1px solid #dc3545; border-radius:10px; padding:10px 20px; font-weight:600;">
                        Cancel
                    </button>
                    <button type="submit" class="btn" 
                            style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:10px 24px; font-weight:700;">
                        <i class="bi bi-check-circle me-2"></i>
                        Confirm Checkout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ✅ Auto-open modal if validation error exists --}}
@if($errors->has('customer_name'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    checkoutModal.show();
});
</script>
@endif

<!-- Bootstrap JS (needed for modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ✅ Auto-focus customer name input when modal opens
document.getElementById('checkoutModal').addEventListener('shown.bs.modal', function () {
    document.getElementById('customer_name').focus();
});

// ✅ Clear validation errors when modal closes
document.getElementById('checkoutModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('customer_name').classList.remove('is-invalid');
});
</script>
</body>
</html>