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

<div class="container-xl py-3">
    {{-- Categories filter (ordered by creation) --}}
    <div class="row mb-2">
        <div class="col-12">
            <div class="overflow-auto py-2" style="white-space:nowrap; -webkit-overflow-scrolling:touch;">
                <a href="{{ route('waiter.cart', ['search' => request('search')]) }}"
                   class="btn btn-sm me-2 mb-2"
                   style="{{ request('category') ? 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' : 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' }}">
                    All
                    <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">{{ $totalProductsCount }}</span>
                </a>

                @foreach($categories as $cat)
                    @php $active = (string) request('category') === (string) ($cat->id ?? $cat->name); @endphp
                    <a href="{{ route('waiter.cart', ['category' => $cat->id, 'search' => request('search')]) }}"
                       class="btn btn-sm me-2 mb-2"
                       style="{{ $active ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                        {{ $cat->name }}
                        <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">
                            {{ $cat->products->count() ?? ($cat->products_count ?? '') }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- LEFT: Menu grouped by category --}}
    <div class="row g-3 align-items-stretch">
        <div class="col-12 col-lg-8">
            <div class="d-flex flex-column h-100">
                <div class="row g-2 mt-1">
                    @foreach($categories as $category)
                        @if($category->products->isEmpty()) @continue @endif
                        <div class="col-12">
                            <h6 class="fw-semibold mb-2">{{ $category->name }}</h6>
                            <div class="row g-2">
                                @foreach($category->products as $product)
                                    <div class="col-12 col-sm-6 col-md-4">
                                        @include('components.product-card', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT: Orders unchanged --}}
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