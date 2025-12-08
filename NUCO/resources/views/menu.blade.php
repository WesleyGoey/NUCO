@extends('layouts.mainlayout')

@section('title', 'Menu')

@section('content')
<div class="container-xl py-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="m-0 fw-bold">Menu</h4>
                    <div class="text-muted small">Showing results — {{ $totalProductsCount }}</div>
                </div>

                <div class="ms-3" style="min-width:220px; max-width:420px;">
                    <form method="GET" action="{{ route('menu') }}" class="d-flex">
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
                <a href="{{ route('menu', ['search' => $search ?? '']) }}"
                   class="btn btn-sm me-2 mb-2"
                   style="{{ $allActive ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                    All
                    <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">{{ $totalProductsCount }}</span>
                </a>

                @php
                    // Untuk tombol kategori, kita perlu semua kategori (bukan yang sudah difilter)
                    $allCategoriesForButtons = \App\Models\Category::orderBy('id')->get();
                @endphp
                
                @foreach($allCategoriesForButtons as $cat)
                    @php
                        $catId = (string) $cat->id;
                        $active = !empty($selectedCategory) && (string)$selectedCategory === $catId;
                        // Hitung produk untuk kategori ini
                        $productCount = $cat->products()->count();
                    @endphp
                    <a href="{{ route('menu', ['category' => $catId, 'search' => $search ?? '']) }}"
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

   {{-- Products grouped by category --}}
   <div class="row g-3">
        <section class="col-12">
            @forelse($categories as $category)
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
                                    {{-- Image placeholder --}}
                                    <div style="border-radius:12px 12px 0 0; overflow:hidden;">
                                        <div class="ratio ratio-4x3">
                                            @if(!empty($product->image_path))
                                                <img src="{{ asset('storage/' . $product->image_path) }}" 
                                                     alt="{{ $product->name }}" 
                                                     style="width:100%; height:100%; object-fit:cover; display:block;">
                                            @else
                                                {{-- Empty placeholder untuk gambar --}}
                                                <div class="d-flex align-items-center justify-content-center w-100 h-100"
                                                     style="background:#FFFFFF; border:1px dashed rgba(0,0,0,0.06);">
                                                    {{-- Area kosong untuk gambar --}}
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
                                            <div class="fw-bold" style="color:#A4823B;">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </div>

                                            <div>
                                                @if(!empty($product->is_available) && $product->is_available)
                                                    <span class="badge" style="background:#E6F9EE;color:#2D7A3B;border-radius:6px;padding:6px 10px;">Available</span>
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
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:3rem;"></i>
                    <p class="mt-2">No products found</p>
                </div>
            @endforelse
        </section>
    </div>
</div>
@endsection