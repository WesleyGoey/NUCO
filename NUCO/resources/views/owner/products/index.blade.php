@extends('layouts.mainlayout')

@section('title', 'Products')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold" style="color:#4b3028;">Products Management</h3>
            <div class="small text-muted">Manage menu & prices</div>
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

    <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead style="background:#F5F0E5;">
                    <tr>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">#</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Image</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Name</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Category</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Price</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Status</th>
                        <th class="px-4 py-3 text-center" style="color:#4b3028; font-weight:700;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr style="border-bottom:1px solid #E9E6E2;">
                            <td class="px-4 py-3">{{ $product->id }}</td>
                            <td class="px-4 py-3">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                                         alt="{{ $product->name }}"
                                         style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                @else
                                    <div style="width:50px;height:50px;background:#F5F0E5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-image" style="color:#A4823B;font-size:1.25rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <div class="small text-muted">{{ Str::limit($product->description, 40) }}</div>
                            </td>
                            <td class="px-4 py-3">
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
                                <span class="badge" style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-weight:600;padding:6px 12px;border-radius:8px;">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="fw-bold" style="color:#A4823B;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($product->is_available)
                                    <span class="badge" style="background:#E6F9EE;color:#2D7A3B;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        Available
                                    </span>
                                @else
                                    <span class="badge" style="background:#FFECEC;color:#C0392B;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        Unavailable
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('owner.products.edit', $product) }}" 
                                       class="btn btn-sm" 
                                       style="background:#A4823B;color:#F5F0E5;border-radius:8px;padding:6px 14px;font-weight:600;">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('owner.products.destroy', $product) }}" method="POST" 
                                          onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:6px 14px;font-weight:600;">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size:2rem;color:#E9E6E2;"></i>
                                <div class="mt-2">No products found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-3 border-top" style="background:#FAFAFA;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection