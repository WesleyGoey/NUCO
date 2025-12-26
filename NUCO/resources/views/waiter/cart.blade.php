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

@php
    $selectedTable = session('selected_table') ?? null;
    $cart = session('waiter_cart', []);
    $cartCount = array_sum(array_map(fn($i)=>(int)($i['quantity'] ?? 0), $cart ?: []));
    $cartTotal = array_sum(array_map(fn($i)=>(int)($i['subtotal'] ?? 0), $cart ?: []));
@endphp

<div class="container-xl py-4">
   {{-- Header & Search --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
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

                <div class="ms-3" style="min-width:220px; max-width:420px;">
                    <form method="GET" action="{{ route('waiter.cart') }}" class="d-flex">
                        @if(!empty($selectedCategory))
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                        @endif
                        <input name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                               placeholder="Search menu..." style="border-radius:10px;border:1px solid #E9E6E2;padding:8px;" />
                        <button type="submit" class="btn btn-sm ms-2"
                                style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 12px;font-weight:600;">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            {{-- Category Filter Buttons --}}
            <div class="overflow-auto py-2" style="white-space:nowrap; -webkit-overflow-scrolling:touch;">
                @php $allActive = empty($selectedCategory); @endphp
                <a href="{{ route('waiter.cart', ['search' => $search ?? '']) }}"
                   class="btn btn-sm me-2 mb-2"
                   style="{{ $allActive ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                    All
                    <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">{{ $totalProductsCount }}</span>
                </a>

                @php
                    $allCategoriesForButtons = \App\Models\Category::orderBy('id')->get();
                @endphp
                
                @foreach($allCategoriesForButtons as $cat)
                    @php
                        $catId = (string) $cat->id;
                        $active = !empty($selectedCategory) && (string)$selectedCategory === $catId;
                        $productCount = $cat->products()->count();
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
        </div>
    </div>

    {{-- Main Content: Menu (2/3) + Orders (1/3) --}}
    <div class="row g-3">
        {{-- LEFT: Menu Products (2/3) --}}
        <div class="col-12 col-lg-8">
            @php
                $totalDisplayed = $categories->sum(function($c) {
                    return isset($c->products) ? $c->products->count() : 0;
                });
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
                    @if(empty($category->products) || $category->products->isEmpty())
                        @continue
                    @endif

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color:#4b3028;">{{ $category->name }}</h5>
                        <div class="row g-3">
                            @foreach($category->products as $product)
                                <div class="col-12 col-sm-6 col-md-4">
                                    {{-- Product Card (inline) --}}
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

                                                <div>
                                                    @if(!empty($product->is_available) && $product->is_available)
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
                                                        <span class="badge bg-secondary">Unavailable</span>
                                                    @endif
                                                </div>
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
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1 me-2">
                                                <div class="fw-bold">{{ $item['name'] }}</div>
                                                <div class="small text-muted">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</div>
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
                                                <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                                <input type="text" name="note" 
                                                       value="{{ $item['note'] ?? '' }}" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Add note (e.g., no spicy)..."
                                                       style="border-radius:6px; font-size:0.85rem;">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" 
                                                        style="border-radius:6px; padding:4px 8px;">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            {{-- Quantity Controls --}}
                                            <div class="d-flex align-items-center gap-2">
                                                <form method="POST" action="{{ route('waiter.cart.update') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                                    <input type="hidden" name="quantity" value="{{ max(1, ($item['quantity'] ?? 1) - 1) }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" 
                                                            style="width:30px; height:30px; padding:0; border-radius:6px;"
                                                            {{ ($item['quantity'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                </form>

                                                <span class="fw-bold" style="min-width:30px; text-align:center;">{{ $item['quantity'] ?? 1 }}</span>

                                                <form method="POST" action="{{ route('waiter.cart.update') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                                    <input type="hidden" name="quantity" value="{{ ($item['quantity'] ?? 1) + 1 }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" 
                                                            style="width:30px; height:30px; padding:0; border-radius:6px;">
                                                        <i class="bi bi-plus"></i>
                                                    </form>
                                                </div>

                                                {{-- Delete Button --}}
                                                <form method="POST" action="{{ route('waiter.cart.remove') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            style="width:30px; height:30px; padding:0; border-radius:6px;"
                                                            onclick="return confirm('Remove this item from cart?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
                                <div class="fw-bold">Total</div>
                                <div class="fw-bold" style="color:#A4823B; font-size:1.15rem;">
                                    Rp {{ number_format($cartTotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endif

                        {{-- Action Buttons - Always visible --}}
                        <div class="d-grid gap-2">
                            @if(!empty($cart))
                                <form method="POST" action="{{ route('waiter.cart.checkout') }}">
                                    @csrf
                                    <button type="submit" class="btn w-100 d-flex align-items-center justify-content-center"
                                            style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:10px;font-weight:700;"
                                            onclick="return confirm('Create order from cart and send to kitchen?')">
                                        <i class="bi bi-bag-check me-2"></i>
                                        Checkout
                                    </button>
                                </form>
 
                                 <form method="POST" action="{{ route('waiter.cart.clear') }}">
                                     @csrf
                                     <button type="submit" class="btn btn-outline-secondary w-100" 
                                             style="border-radius:8px;padding:8px;"
                                             onclick="return confirm('Clear all items from cart?')">
                                         <i class="bi bi-trash3 me-1"></i> Clear Cart
                                     </button>
                                 </form>
 
                                 <form method="POST" action="{{ route('waiter.tables.cancel') }}">
                                     @csrf
                                     <button type="submit" class="btn btn-outline-danger w-100" 
                                             style="border-radius:8px;padding:8px;"
                                             onclick="return confirm('Cancel order and return to table selection? {{ !empty($cart) ? 'Cart will be cleared and table will be released.' : 'Table will be released.' }}')">
                                         <i class="bi bi-x-circle me-1"></i> Cancel Order
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>