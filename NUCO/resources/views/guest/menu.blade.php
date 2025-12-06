@extends('layouts.mainlayout')

@section('title', 'Menu')

@section('content')
<div class="container-xl py-4">
    {{-- CATEGORIES: full-width pills on top (visible on all sizes) --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="overflow-auto py-2" style="white-space:nowrap; -webkit-overflow-scrolling:touch;">
                <a href="{{ route('menu', ['search' => request('search')]) }}"
                   class="btn btn-sm me-2 mb-2"
                   style="{{ request('category') ? 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' : 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' }}">
                    All
                    <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">{{ $totalProductsCount }}</span>
                </a>

                @foreach($categories as $cat)
                    @php $active = request('category') === $cat->id; @endphp
                    <a href="{{ route('menu', ['category' => $cat->id, 'search' => request('search')]) }}"
                       class="btn btn-sm me-2 mb-2"
                       style="{{ $active ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                        {{ $cat->name }}
                        <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">{{ $cat->products_count ?? '' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- keep layout single-column products area (no sidebar) --}}
        <!-- Right: products (image on top, details below) -->
        <section class="col-12">
             <div class="d-flex justify-content-between align-items-center mb-3">
                 <div>
                     <h4 class="m-0 fw-bold">Menu</h4>
                     <div class="text-muted small">Showing results — {{ is_object($products) || method_exists($products, 'count') ? $products->count() : count($products) }} items</div>
                 </div>

                 {{-- SEARCH --}}
                 <div class="ms-2" style="min-width:240px;">
                     <form method="GET" action="{{ route('menu') }}" class="d-flex">
                         <input type="hidden" name="category" value="{{ request('category') }}">
                         <input name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                                placeholder="Search menu..."
                                style="border-radius:10px;border:1px solid #E9E6E2;padding:10px;" />
                         <button type="submit" class="btn btn-sm ms-2"
                                 style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 10px;">Search</button>
                     </form>
                 </div>
             </div>

             <div class="row g-3">
                 @forelse($products as $product)
                     <div class="col-12 col-md-6">
                         <div class="card h-100 shadow-sm border-0" style="border-radius:18px; overflow:hidden;">
                             @if($product->image_path)
                                 <img src="{{ asset('storage/' . $product->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height:220px; object-fit:cover; border-radius:18px 18px 0 0; width:100%;">
                             @else
                                 <div class="d-flex align-items-center justify-content-center" style="height:220px; background:#F5F0E5; border-radius:18px 18px 0 0;">
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
                                                <span class="badge" style="background:#E6F9EE;color:#2D7A3B;border-radius:6px;padding:4px 8px;">Available</span>
                                            @else
                                                <span class="badge bg-secondary">Unavailable</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div>
                                        <a href="{{ route('menu') }}#product-{{ $product->id }}" class="btn btn-sm"
                                           style="background:#A4823B;color:#F5F0E5;border-radius:8px;padding:6px 10px;">View</a>
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