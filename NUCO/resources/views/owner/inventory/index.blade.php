@extends('layouts.mainlayout')

@section('title', 'Inventory')

@section('content')
    <div class="container-xl py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0 fw-bold" style="color:#4b3028;">Inventory & Ingredients</h3>
                <div class="small text-muted">Manage ingredient stock</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="small text-muted">Low Stock</div>
                    <div class="fw-bold" style="color:#C0392B; font-size:1.25rem;">{{ $lowStock }}</div>
                </div>
                <a href="{{ route('owner.inventory.create') }}" class="btn"
                    style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 18px;font-weight:700;">
                    <i class="bi bi-plus-lg me-2"></i> Add Ingredient
                </a>
            </div>
        </div>

        {{-- Stock Status Summary --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:12px; border-left: 4px solid #C0392B;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-muted mb-1">Low Stock</div>
                                <div class="h4 mb-0 fw-bold" style="color:#C0392B;">{{ $lowStock }}</div>
                            </div>
                            <i class="bi bi-exclamation-triangle-fill"
                                style="font-size:2rem; color:#C0392B; opacity:0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:12px; border-left: 4px solid #D68910;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-muted mb-1">Medium Stock</div>
                                <div class="h4 mb-0 fw-bold" style="color:#D68910;">{{ $mediumStock }}</div>
                            </div>
                            <i class="bi bi-exclamation-circle-fill"
                                style="font-size:2rem; color:#D68910; opacity:0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:12px; border-left: 4px solid #2D7A3B;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-muted mb-1">Good Stock</div>
                                <div class="h4 mb-0 fw-bold" style="color:#2D7A3B;">{{ $goodStock }}</div>
                            </div>
                            <i class="bi bi-check-circle-fill" style="font-size:2rem; color:#2D7A3B; opacity:0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius:12px;">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('owner.inventory.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" class="form-control"
                        placeholder="Search ingredients..." style="border-radius:10px;">
                    <button type="submit" class="btn"
                        style="background:#A4823B;color:#F5F0E5;border-radius:8px;padding:8px 20px;font-weight:600;">
                        Search
                    </button>
                    @if (!empty($search))
                        <a href="{{ route('owner.inventory.index') }}" class="btn btn-outline-secondary"
                            style="border-radius:8px;">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Ingredients Table --}}
        <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead style="background:#F5F0E5;">
                        <tr>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">#</th>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Name</th>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Current Stock</th>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Min Stock</th>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Unit</th>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Status</th>
                            <th class="px-4 py-3 text-center" style="color:#4b3028; font-weight:700;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingredients as $index => $ingredient)
                            @php
                                $isLow = $ingredient->current_stock <= $ingredient->min_stock;
                                $isMedium =
                                    $ingredient->current_stock > $ingredient->min_stock &&
                                    $ingredient->current_stock <= $ingredient->min_stock * 1.5;
                                $statusBg = $isLow ? '#FFECEC' : ($isMedium ? '#FFF4E6' : '#E6F9EE');
                                $statusColor = $isLow ? '#C0392B' : ($isMedium ? '#D68910' : '#2D7A3B');
                                $statusText = $isLow ? 'Low Stock' : ($isMedium ? 'Medium' : 'Good');
                            @endphp
                            <tr style="border-bottom:1px solid #E9E6E2;">
                                <td class="px-4 py-3">{{ $ingredients->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">{{ $ingredient->name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="fw-bold"
                                        style="color:{{ $statusColor }};">{{ number_format($ingredient->current_stock, 2) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ number_format($ingredient->min_stock, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge"
                                        style="background:#F5F0E5;color:#A4823B;padding:6px 12px;border-radius:8px;font-weight:600;">
                                        {{ strtoupper($ingredient->unit) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge"
                                        style="background:{{ $statusBg }};color:{{ $statusColor }};padding:6px 12px;border-radius:8px;font-weight:600;">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                                        <a href="{{ route('owner.inventory.stock', $ingredient) }}" class="btn btn-sm"
                                            style="background:#2D7A3B;color:#F5F0E5;border-radius:8px;padding:6px 14px;font-weight:600;">
                                            <i class="bi bi-plus-circle me-1"></i> Stock
                                        </a>
                                        <a href="{{ route('owner.inventory.edit', $ingredient) }}" class="btn btn-sm"
                                            style="background:#A4823B;color:#F5F0E5;border-radius:8px;padding:6px 14px;font-weight:600;">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                        <form action="{{ route('owner.inventory.destroy', $ingredient) }}" method="POST"
                                            onsubmit="return confirm('Delete this ingredient?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"
                                                style="border-radius:8px;padding:6px 14px;font-weight:600;">
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
                                    <div class="mt-2">No ingredients found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- ✅ CLOSE CARD HERE --}}
        </div>

        {{-- ✅ PAGINATION OUTSIDE CARD (Below white background) --}}
        @if ($ingredients->lastPage() > 1)
            <div class="d-flex justify-content-center mt-4">
                <nav>
                    <ul class="pagination" style="gap:0.7rem; margin:0; padding:0; list-style:none; display:flex;">
                        @if ($ingredients->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link"
                                    style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; cursor:not-allowed; display:inline-block;">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ $ingredients->appends(['search' => request('search')])->previousPageUrl() }}"
                                    rel="prev"
                                    style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">&laquo;</a>
                            </li>
                        @endif

                        @foreach ($ingredients->links()->elements[0] as $page => $url)
                            @if ($page == $ingredients->currentPage())
                                <li class="page-item active">
                                    <span class="page-link"
                                        style="color:#F5F0E5; background:#A4823B; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:700; display:inline-block; box-shadow:0 4px 12px rgba(164,130,59,0.3);">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ $ingredients->appends(['search' => request('search')])->url($page) }}"
                                        style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        @if ($ingredients->hasMorePages())
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ $ingredients->appends(['search' => request('search')])->nextPageUrl() }}"
                                    rel="next"
                                    style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link"
                                    style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; cursor:not-allowed; display:inline-block;">&raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    </div> {{-- Container end --}}
@endsection
