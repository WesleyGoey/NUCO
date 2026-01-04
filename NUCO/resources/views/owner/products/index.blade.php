@extends('layouts.mainlayout')

@section('title', 'Products')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold" style="color:#4b3028;">Products Management</h3>
            <div class="small text-muted">Manage menu, prices & recipes</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="small text-muted">Available</div>
                <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">{{ $availableCount }}</div>
            </div>
            <a href="{{ route('owner.products.create') }}" class="btn" 
               style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 18px;font-weight:700;">
                <i class="bi bi-plus-lg me-2"></i> Add Product
            </a>
        </div>
    </div>

    {{-- Search & Filter Card --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius:12px; overflow:hidden;">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                {{-- Filters (left, scrollable) --}}
                <div class="overflow-auto" style="white-space:nowrap; -webkit-overflow-scrolling:touch; flex:1;">
                    @php $allActive = empty($selectedCategory); @endphp
                    <a href="{{ route('owner.products.index', ['search' => $search ?? '']) }}"
                       class="btn btn-sm me-2 mb-2"
                       style="{{ $allActive ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                        All
                        <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">{{ $totalProductsCount }}</span>
                    </a>

                    @foreach($categories as $cat)
                        @php
                            $catId = (string) $cat->id;
                            $active = !empty($selectedCategory) && (string)$selectedCategory === $catId;
                            $productCount = $cat->products()->count();
                        @endphp
                        <a href="{{ route('owner.products.index', ['category' => $catId, 'search' => $search ?? '']) }}"
                           class="btn btn-sm me-2 mb-2"
                           style="{{ $active ? 'background:#A4823B;color:#F5F0E5;border:none;font-weight:700;' : 'background:#ffffff;color:#6b6b6b;border:1px solid rgba(164,130,59,0.12);' }}">
                            {{ $cat->name }}
                            <span class="ms-2" style="background:#F5F0E5;color:#A4823B;border-radius:10px;padding:4px 8px;font-size:0.85rem;">
                                {{ $productCount }}
                            </span>
                        </a>
                    @endforeach
                </div>

                {{-- Search (right) --}}
                <div style="min-width:260px;">
                    <form method="GET" action="{{ route('owner.products.index') }}" class="d-flex">
                        @if(!empty($selectedCategory))
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                        @endif
                        <input name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                               placeholder="Search products..." 
                               style="border-radius:10px;border:1px solid #E9E6E2;padding:8px;" />
                        <button type="submit" class="btn btn-sm ms-2"
                                style="background:#A4823B;color:#F5F0E5;border:none;border-radius:8px;padding:6px 12px;font-weight:600;">
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead style="background:#F5F0E5;">
                    <tr>
                        <th class="px-3 py-3" style="color:#4b3028; font-weight:700; width:40px;">#</th>
                        <th class="px-3 py-3" style="color:#4b3028; font-weight:700; width:70px;">Image</th>
                        <th class="px-3 py-3" style="color:#4b3028; font-weight:700;">Product</th>
                        <th class="px-3 py-3 text-center" style="color:#4b3028; font-weight:700; width:110px;">Category</th>
                        <th class="px-3 py-3 text-end" style="color:#4b3028; font-weight:700; width:100px;">Price</th>
                        <th class="px-3 py-3 text-center" style="color:#4b3028; font-weight:700; width:100px;">Recipe</th>
                        <th class="px-3 py-3 text-center" style="color:#4b3028; font-weight:700; width:100px;">Status</th>
                        <th class="px-3 py-3 text-center" style="color:#4b3028; font-weight:700; width:190px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                        <tr style="border-bottom:1px solid #E9E6E2;">
                            <td class="px-3 py-2">{{ $products->firstItem() + $index }}</td>
                            
                            {{-- Image --}}
                            <td class="px-3 py-2">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                                         alt="{{ $product->name }}"
                                         style="width:45px;height:45px;object-fit:cover;border-radius:8px;">
                                @else
                                    {{-- ✅ UPDATED: Coffee/Restaurant icon (smaller 18px for table) --}}
                                    <div style="width:45px;height:45px;background:#F5F0E5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A4823B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                                            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                                            <line x1="6" y1="1" x2="6" y2="4"/>
                                            <line x1="10" y1="1" x2="10" y2="4"/>
                                            <line x1="14" y1="1" x2="14" y2="4"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            
                            {{-- Product Name & Description (Reduced spacing) --}}
                            <td class="px-3 py-2">
                                <div class="fw-semibold text-truncate mb-1" style="max-width:250px;">{{ $product->name }}</div>
                                <div class="small text-muted text-truncate" style="max-width:250px;">{{ Str::limit($product->description, 50) }}</div>
                            </td>
                            
                            {{-- Category Badge --}}
                            <td class="px-3 py-2 text-center">
                                @php
                                    $categoryColors = [
                                        'Main Course' => ['bg' => '#FFF4E6', 'text' => '#D68910'],
                                        'Snacks' => ['bg' => '#FFE6E6', 'text' => '#C0392B'],
                                        'Drinks' => ['bg' => '#E6F0FF', 'text' => '#2E5C8A'],
                                        'Dessert' => ['bg' => '#F3E6FF', 'text' => '#7B3FA8'],
                                    ];
                                    $categoryName = $product->category->name ?? 'Other';
                                    $colors = $categoryColors[$categoryName] ?? ['bg' => '#E6F9EE', 'text' => '#2D7A3B'];
                                @endphp
                                <span class="badge" style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-weight:600;padding:5px 10px;border-radius:6px;font-size:0.8rem;">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>
                            
                            {{-- Price (Single Line) --}}
                            <td class="px-3 py-2 text-end">
                                <span class="fw-bold text-nowrap" style="color:#A4823B;">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </td>
                            
                            {{-- Ingredients Count --}}
                            <td class="px-3 py-2 text-center">
                                @php
                                    $ingredientCount = $product->ingredients()->count();
                                @endphp
                                @if($ingredientCount > 0)
                                    <span class="badge" style="background:#E6F9EE;color:#2D7A3B;font-weight:600;padding:5px 10px;border-radius:6px;font-size:0.8rem;">
                                        <i class="bi bi-box-seam me-1"></i>{{ $ingredientCount }}
                                    </span>
                                @else
                                    <span class="badge" style="background:#F5F5F5;color:#9E9E9E;font-weight:600;padding:5px 10px;border-radius:6px;font-size:0.8rem;">
                                        -
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Availability Status (Available/Unavailable) --}}
                            <td class="px-3 py-2 text-center">
                                @if($product->is_available)
                                    <span class="badge" style="background:#E6F9EE;color:#2D7A3B;font-weight:600;padding:5px 10px;border-radius:6px;font-size:0.8rem;white-space:nowrap;">
                                        Available
                                    </span>
                                @else
                                    <span class="badge" style="background:#FFECEC;color:#C0392B;font-weight:600;padding:5px 10px;border-radius:6px;font-size:0.8rem;white-space:nowrap;">
                                        Unavailable
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Actions (Compact Button Group - Single Line) --}}
                            <td class="px-3 py-2">
                                <div class="btn-group" role="group" style="white-space:nowrap;">
                                    <a href="{{ route('owner.products.recipe', $product) }}" 
                                       class="btn btn-sm" 
                                       title="Recipe"
                                       style="background:#2D7A3B;color:#F5F0E5;border-radius:6px 0 0 6px;padding:5px 10px;font-weight:600;">
                                        <i class="bi bi-card-list"></i>
                                    </a>
                                    <a href="{{ route('owner.products.edit', $product) }}" 
                                       class="btn btn-sm" 
                                       title="Edit"
                                       style="background:#A4823B;color:#F5F0E5;border-radius:0;padding:5px 10px;font-weight:600;">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('owner.products.destroy', $product) }}" method="POST" 
                                          onsubmit="return confirm('Delete this product?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" 
                                                title="Delete"
                                                style="border-radius:0 6px 6px 0;padding:5px 10px;font-weight:600;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size:2rem;color:#E9E6E2;"></i>
                                <div class="mt-2">
                                    @if(!empty($search))
                                        No products found for "{{ $search }}"
                                    @else
                                        No products found.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-3 border-top" style="background:#FAFAFA;">
                {{ $products->appends(['search' => $search, 'category' => $selectedCategory])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection