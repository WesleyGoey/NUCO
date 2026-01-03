@extends('layouts.mainlayout')

@section('title', 'Menu')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Menu</h1>
        <small class="text-muted">Browse our dishes</small>
    </div>

    <form method="GET" action="{{ route('menu') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" value="{{ $search }}" class="form-control"
                   placeholder="Search products..." style="border-radius:10px 0 0 10px;">
            <button type="submit" class="btn" style="background:#A4823B;color:#F5F0E5;border-radius:0 10px 10px 0;">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('menu') }}" class="btn {{ empty($selectedCategory) ? 'btn-primary' : 'btn-outline-secondary' }}" 
           style="border-radius:8px;padding:8px 16px;font-weight:600;">All</a>
        @foreach($allCategoriesForButtons as $cat)
            <a href="{{ route('menu', ['category' => $cat->id]) }}" 
               class="btn {{ $selectedCategory == $cat->id ? 'btn-primary' : 'btn-outline-secondary' }}" 
               style="border-radius:8px;padding:8px 16px;font-weight:600;">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    <div class="row g-3">
        @if($categories->isEmpty())
            <div class="col-12">
                <div class="alert alert-info">No categories found.</div>
            </div>
        @else
            @foreach($categories as $category)
                @if($category->products->isNotEmpty())
                    <div class="col-12">
                        <h4 class="mb-3 fw-bold" style="color:#4b3028;">{{ $category->name }}</h4>
                        <div class="row g-3">
                            @foreach($category->products as $product)
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="card h-100 shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                                        @if($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}" 
                                                 class="card-img-top" alt="{{ $product->name }}"
                                                 style="height:180px; object-fit:cover;">
                                        @else
                                            <div style="height:180px; background:#F5F0E5; display:flex; align-items:center; justify-content:center;">
                                                <i class="bi bi-image" style="font-size:3rem; color:#A4823B; opacity:0.3;"></i>
                                            </div>
                                        @endif

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title fw-bold mb-2" style="color:#4b3028;">{{ $product->name }}</h5>
                                            <p class="card-text small text-muted mb-3">{{ Str::limit($product->description ?? '-', 80) }}</p>

                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <div class="fw-bold" style="color:#A4823B;">
                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </div>

                                                <div>
                                                    {{-- ✅ UPDATED: Check both is_available and in_stock --}}
                                                    @if(!empty($product->is_available) && $product->is_available && ($product->in_stock ?? true))
                                                        <span class="badge" style="background:#E6F9EE;color:#2D7A3B;border-radius:6px;padding:6px 10px;">Available</span>
                                                    @else
                                                        <span class="badge bg-secondary">Out of stock</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</div>
@endsection