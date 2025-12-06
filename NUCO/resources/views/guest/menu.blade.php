@extends('layouts.mainlayout')

@section('title', 'Menu')

@section('content')
<div class="container-xl py-4">
    <div class="row g-3">
        <!-- Left: categories (clickable, only bold when active) -->
        <aside class="col-12 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3" style="background: #F5F0E5;">
                    <h6 class="fw-bold mb-3" style="color:#000;">Categories</h6>

                    <ul class="list-unstyled mb-0">
                        <li class="py-2 d-flex justify-content-between align-items-center">
                            <a href="{{ route('menu', ['search' => request('search')]) }}" class="text-decoration-none {{ request('category') ? 'text-muted' : 'fw-bold text-dark' }}">All</a>
                            <span class="badge bg-white text-muted">{{ $totalProductsCount }}</span>
                        </li>

                        @foreach($categories ?? [] as $cat)
                            @php $active = request('category') === $cat->id; @endphp
                            <li class="py-2 d-flex justify-content-between align-items-center">
                                <a href="{{ route('menu', ['category' => $cat->id, 'search' => request('search')]) }}"
                                   class="text-decoration-none {{ $active ? 'fw-bold text-dark' : 'text-muted' }}">
                                    {{ $cat->name }}
                                </a>
                                <span class="badge bg-white text-muted">{{ $cat->products_count ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Right: products (image on top, details below) -->
        <section class="col-12 col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="m-0 fw-bold">Menu</h4>
                    <div class="text-muted small">Showing results — {{ is_object($products) || method_exists($products, 'count') ? $products->count() : count($products) }} items</div>
                </div>

                {{-- SEARCH --}}
                <div class="ms-2" style="min-width:240px;">
                    <form method="GET" action="{{ route('menu') }}" class="d-flex">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Search menu..." />
                        <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">Search</button>
                    </form>
                </div>
            </div>

            <div class="row g-3">
                @forelse($products as $product)
                    <div class="col-12 col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height:220px; object-fit:cover; border-radius:18px 18px 0 0;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height:220px; background:#F5F0E5; border-radius:18px 18px 0 0;">
                                    <span class="fw-bold" style="font-size:28px;color:#000;">{{ strtoupper(substr($product->name,0,1)) }}</span>
                                </div>
                            @endif

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
                                        <div class="small mt-1">
                                            @if($product->is_available)
                                                <span class="badge" style="background:#E6F9EE;color:#2D7A3B;border-radius:6px;">Available</span>
                                            @else
                                                <span class="badge bg-secondary">Unavailable</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div>
                                        <a href="{{ route('menu') }}#product-{{ $product->id }}" class="btn btn-sm" style="background:#A4823B;color:#F5F0E5;border-radius:8px;">View</a>
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
        </section>
    </div>
</div>
@endsection