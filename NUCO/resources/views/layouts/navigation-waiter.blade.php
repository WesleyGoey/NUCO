<nav class="navbar navbar-expand-lg" style="background-color: #A4823B; box-shadow: 0 2px 4px rgba(0,0,0,0.25);">
    <div class="container-fluid position-relative" style="min-height:70px;">
        <div class="d-flex align-items-center justify-content-between" style="height:70px; width:100%;">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" style="margin-left:8px;">
                <span style="background:#F5F0E5;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name', 'NUCO') }}" style="height:40px;width:40px;object-fit:contain;">
                </span>
            </a>

            <!-- Desktop links (right) -->
            <div class="d-none d-lg-flex align-items-center ms-auto" style="gap:2rem;">
                <ul class="navbar-nav mb-0 d-flex flex-row align-items-center" style="gap:1.5rem;">
                    <li class="nav-item"><a class="nav-link fs-5" href="{{ route('waiter.tables') }}">Tables</a></li>
                    <li class="nav-item"><a class="nav-link fs-5" href="{{ route('menu') }}">Menu</a></li>
                    <li class="nav-item"><a class="nav-link fs-5" href="{{ route('discounts') }}">Discounts</a></li>
                    <li class="nav-item"><a class="nav-link fs-5" href="{{ route('waiter.orders') }}">Orders</a></li>
                </ul>

                <div class="d-flex align-items-center" style="gap:0.6rem;">
                    @php $hasTable = session()->has('selected_table'); @endphp

                    @guest
                        <a class="btn px-3 py-2" href="{{ route('login') }}" style="background:#F5F0E5;color:#A4823B;border-radius:20px;box-shadow:0 2px 8px rgba(0,0,0,0.06);text-decoration:none;font-weight:400;">Login</a>
                    @else
                        @if($hasTable)
                            <button class="btn px-3 py-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#waiterCart" aria-controls="waiterCart" style="background:#F5F0E5;color:#A4823B;border-radius:20px;box-shadow:0 2px 8px rgba(0,0,0,0.06);font-weight:400;">
                                Cart
                            </button>
                        @else
                            <button class="btn px-3 py-2" disabled style="background:#E9E6E2;color:#6b6b6b;border-radius:20px;border:none;font-weight:400;">Cart</button>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="background:#F5F0E5;color:#A4823B;border-radius:20px;border:none;font-weight:400;margin-left:8px;">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>

            <!-- Mobile toggler (right) -->
            <button class="navbar-toggler d-lg-none border-0 ms-auto" type="button" data-bs-toggle="collapse"
                data-bs-target="#nucoNavbarWaiter" aria-controls="nucoNavbarWaiter" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="bi-list"></i>
            </button>
        </div>

        <!-- Collapse: mobile vertical panel (pushes content) -->
        <div class="collapse navbar-collapse" id="nucoNavbarWaiter">
            <div class="w-100">
                <ul class="navbar-nav d-lg-none flex-column" style="gap:0.6rem; padding:0.75rem 1rem; margin:0;">
                    <li class="nav-item"><a class="nav-link" href="{{ route('waiter.tables') }}">Tables</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('menu') }}">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('discounts') }}">Discounts</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('waiter.orders') }}">Orders</a></li>

                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @else
                        @if(session()->has('selected_table'))
                            <li class="nav-item"><a class="nav-link" href="{{ route('waiter.cart') }}">Cart</a></li>
                        @else
                            <li class="nav-item"><a class="nav-link text-muted" href="javascript:void(0);" title="Select table first">Cart</a></li>
                        @endif

                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="btn w-100 text-start">Logout</button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </div>

    <div style="height:8px;background:#4CAF50;border-radius:0 0 8px 8px;"></div>
</nav>

<!-- Offcanvas cart (Bootstrap) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="waiterCart" aria-labelledby="waiterCartLabel">
    <div class="offcanvas-header" style="background:#A4823B; color:#F5F0E5;">
        <h5 id="waiterCartLabel">Cart</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @if(session()->has('selected_table'))
            <p class="mb-2">Table: <strong>{{ session('selected_table.table_number') ?? session('selected_table') }}</strong></p>
            @php $cart = session('waiter_cart', []); @endphp
            @if(empty($cart))
                <div class="text-muted">Cart is empty.</div>
            @else
                <ul class="list-group mb-3">
                    @foreach($cart as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">{{ $item['name'] ?? 'Item' }}</div>
                                <div class="small text-muted">x{{ $item['quantity'] ?? 1 }}</div>
                            </div>
                            <div>Rp {{ number_format($item['subtotal'] ?? 0,0,',','.') }}</div>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('waiter.cart') }}" class="btn w-100" style="background:#A4823B;color:#F5F0E5;border-radius:8px;">Open Cart Page</a>
            @endif
        @else
            <div class="text-muted">Select a table first to use the cart.</div>
            <a href="{{ route('waiter.tables') }}" class="btn btn-sm mt-2" style="background:#F5F0E5;color:#A4823B;border-radius:6px;">Select Table</a>
        @endif
    </div>
</div>

{{-- Desktop inline cart panel when on menu page and table selected --}}
@if(request()->routeIs('menu') && session()->has('selected_table'))
    <div id="waiter-inline-cart" style="position:fixed; right:1rem; top:88px; width:320px; max-height:75vh; overflow:auto; z-index:1050;">
        <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; background:#FFFFFF;">
            <div class="card-body">
                <h5 class="mb-2">Cart (Table: {{ session('selected_table.table_number') ?? session('selected_table') }})</h5>
                @php $cart = session('waiter_cart', []); @endphp
                @if(empty($cart))
                    <div class="text-muted">Cart is empty.</div>
                @else
                    <ul class="list-unstyled mb-3">
                        @foreach($cart as $item)
                            <li class="d-flex justify-content-between">
                                <div>
                                    <div class="fw-bold">{{ $item['name'] ?? 'Item' }}</div>
                                    <div class="small text-muted">x{{ $item['quantity'] ?? 1 }}</div>
                                </div>
                                <div>Rp {{ number_format($item['subtotal'] ?? 0,0,',','.') }}</div>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('waiter.cart') }}" class="btn" style="background:#A4823B;color:#F5F0E5;border-radius:8px;">Open Cart</a>
                @endif
            </div>
        </div>
    </div>
@endif