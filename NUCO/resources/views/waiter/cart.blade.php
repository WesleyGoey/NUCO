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

<div class="container-xl py-3"><!-- reduced vertical padding -->

    {{-- Categories filter --}}
    <div class="row mb-2">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="m-0 fw-bold">Menu</h4>
                    <div class="text-muted small">Showing results — {{ is_object($products) || method_exists($products, 'count') ? $products->count() : count($products) }} items</div>
                </div>

                <div class="ms-3" style="min-width:220px; max-width:420px;">
                    <form method="GET" action="{{ route('waiter.cart') }}" class="d-flex">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                               placeholder="Search menu..."
                               style="border-radius:10px;border:1px solid #E9E6E2;padding:8px;" />
                        <button type="submit" class="btn btn-sm ms-2"
                                style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 12px;font-weight:600;">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            <div class="overflow-auto py-2" style="white-space:nowrap; -webkit-overflow-scrolling:touch;">
                <a href="{{ route('waiter.cart', ['search' => request('search')]) }}"
                   class="btn btn-sm me-2 mb-2"
                   style="{{ request('category') ? 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' : 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' }}">
                    All
                    <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">{{ $totalProductsCount }}</span>
                </a>

                @foreach($categories as $cat)
                    @php $active = (string) request('category') === (string) $cat->id; @endphp
                    <a href="{{ route('waiter.cart', ['category' => $cat->id, 'search' => request('search')]) }}"
                       class="btn btn-sm me-2 mb-2"
                       style="{{ $active ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                        {{ $cat->name }}
                        <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">
                            {{ $cat->products_count ?? '' }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-3 align-items-stretch"><!-- parent row: stretch columns to same height -->
        <!-- LEFT: Menu -->
        <div class="col-12 col-lg-8">
            <div class="d-flex flex-column h-100">
                <div class="row g-2 mt-1">
                    @forelse($products as $product)
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card h-100 shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                                <div style="border-radius:12px 12px 0 0; overflow:hidden;">
                                    <div class="ratio ratio-4x3">
                                        @if(!empty($product->image_path))
                                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background:#F5F0E5;">
                                                <span class="fw-bold" style="font-size:28px;color:#000;">{{ strtoupper(substr($product->name,0,1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold mb-1 text-truncate">{{ $product->name }}</h5>
                                    @if(!empty($product->description))
                                        <p class="card-text text-muted small mb-2" style="line-height:1.3;">{{ $product->description }}</p>
                                    @endif

                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold" style="color:#A4823B;">
                                                Rp {{ number_format($product->price,0,',','.') }}
                                            </div>
                                        </div>

                                        <div>
                                            @php
                                                $canAdd = $selectedTable && auth()->check() && method_exists(auth()->user(), 'isWaiter') && auth()->user()->isWaiter();
                                            @endphp

                                            @if($product->is_available && $canAdd)
                                                <form method="POST" action="{{ route('waiter.cart.add') }}" class="d-inline-block">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-sm"
                                                            style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 10px;font-weight:600;">
                                                        Add
                                                    </button>
                                                </form>
                                            @elseif($product->is_available)
                                                <button class="btn btn-sm" disabled
                                                        style="background:#f0f0f0;color:#8a8a8a;border-radius:8px;padding:6px 10px;">Select table</button>
                                            @else
                                                <span class="badge bg-secondary">Unavailable</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center text-muted py-4">No products found.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT: Orders -->
        <div class="col-12 col-lg-4 d-flex">
            <div class="card w-100 shadow-sm border-0 d-flex flex-column h-100">
                <div class="card-body d-flex flex-column">
                    <div>
                        <h6 class="mb-0 fw-bold">Orders</h6>
                        <div class="small text-muted">Table {{ $selectedTable['table_number'] ?? '-' }} — {{ $cartCount }} items</div>
                    </div>

                    <div class="mt-3 flex-grow-1 overflow-auto">
                        @if(empty($cart))
                            <div class="text-muted">Cart is empty. Add items from the menu.</div>
                        @else
                            <ul class="list-group mb-3">
                                @foreach($cart as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $item['name'] }}</div>
                                            <div class="small text-muted">x{{ $item['quantity'] }}</div>
                                        </div>
                                        <div>Rp {{ number_format($item['subtotal'] ?? 0,0,',','.') }}</div>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="text-muted small">Total</div>
                                <div class="fw-bold">Rp {{ number_format($cartTotal,0,',','.') }}</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button class="btn" disabled style="background:#A4823B;color:#F5F0E5;border-radius:8px;padding:10px;font-weight:700;">Checkout (implement)</button>

                                <form method="POST" action="{{ route('waiter.cart.add') }}" onsubmit="event.preventDefault(); if(confirm('Clear cart?')) { fetch('{{ route('waiter.cart.add') }}', { method:'POST', headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Content-Type':'application/json'}, body: JSON.stringify({clear:1}) }).then(()=>location.reload()) }">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary" style="border-radius:8px;padding:8px 10px;">Clear</button>
                                </form>
                            </div>
                        @endif
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