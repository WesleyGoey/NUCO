@extends('layouts.mainlayout')

@section('title', 'Inventory')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Stock Inventory</h3>
            <div class="small text-muted">Monitor ingredient availability</div>
        </div>
        <div class="text-end">
            <div class="small text-muted">Total Ingredients</div>
            <div class="fw-bold" style="color:#A4823B; font-size:1.5rem;">{{ $ingredients->count() }}</div>
        </div>
    </div>

    {{-- Stock Status Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0" style="border-radius:12px; border-left: 4px solid #C0392B;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted mb-1">Low Stock</div>
                            <div class="h4 mb-0 fw-bold" style="color:#C0392B;">{{ $lowStock }}</div>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem; color:#C0392B; opacity:0.2;"></i>
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
                        <i class="bi bi-exclamation-circle-fill" style="font-size:2rem; color:#D68910; opacity:0.2;"></i>
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
        <div class="card-body">
            <form method="GET" action="{{ route('chef.inventory') }}" class="d-flex gap-2">
                <input type="text" name="search" value="{{ $search }}" 
                       class="form-control" 
                       placeholder="Search ingredients..." 
                       style="border-radius:10px; border:1px solid #E9E6E2; padding:10px;">
                <button type="submit" class="btn" 
                        style="background:#A4823B; color:#F5F0E5; border:none; border-radius:10px; padding:10px 24px; font-weight:700; white-space:nowrap;">
                    <i class="bi bi-search me-2"></i>
                    Search
                </button>
                @if(!empty($search))
                    <a href="{{ route('chef.inventory') }}" class="btn btn-outline-secondary" 
                       style="border-radius:10px; padding:10px 24px; white-space:nowrap;">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Ingredients Table --}}
    <div class="card shadow-sm border-0" style="border-radius:14px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#F5F0E5;">
                        <tr>
                            <th class="px-4 py-3">Ingredient</th>
                            <th class="px-4 py-3">Current Stock</th>
                            <th class="px-4 py-3">Min. Stock</th>
                            <th class="px-4 py-3">Unit</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingredients as $ingredient)
                            @php
                                $stockPercent = $ingredient->min_stock > 0 
                                    ? ($ingredient->current_stock / $ingredient->min_stock) * 100 
                                    : 100;
                                
                                if ($ingredient->current_stock <= $ingredient->min_stock) {
                                    $statusColor = '#C0392B';
                                    $statusBg = '#FFECEC';
                                    $statusText = 'Low Stock';
                                    $statusIcon = 'bi-exclamation-triangle-fill';
                                } elseif ($ingredient->current_stock <= $ingredient->min_stock * 1.5) {
                                    $statusColor = '#D68910';
                                    $statusBg = '#FFF4E6';
                                    $statusText = 'Medium';
                                    $statusIcon = 'bi-exclamation-circle-fill';
                                } else {
                                    $statusColor = '#2D7A3B';
                                    $statusBg = '#E6F9EE';
                                    $statusText = 'Good';
                                    $statusIcon = 'bi-check-circle-fill';
                                }
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width:40px; height:40px; background:{{ $statusBg }};">
                                            <i class="bi bi-box-seam" style="color:{{ $statusColor }}; font-size:1.2rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $ingredient->name }}</div>
                                            <div class="small text-muted">ID: #{{ $ingredient->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="color:{{ $statusColor }}; font-size:1.1rem;">
                                        {{ number_format($ingredient->current_stock, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-muted">
                                        {{ number_format($ingredient->min_stock, 2) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge" style="background:#F5F0E5; color:#A4823B; padding:6px 12px; border-radius:8px; font-weight:700;">
                                        {{ strtoupper($ingredient->unit) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge d-inline-flex align-items-center" 
                                          style="background:{{ $statusBg }}; color:{{ $statusColor }}; padding:8px 14px; border-radius:8px; font-weight:700;">
                                        <i class="bi {{ $statusIcon }} me-2"></i>
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size:3rem; color:#D0D0D0;"></i>
                                    <p class="text-muted mt-3">
                                        @if(!empty($search))
                                            No ingredients found for "{{ $search }}"
                                        @else
                                            No ingredients in inventory
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mt-4">
        <div class="card shadow-sm border-0" style="border-radius:12px; background:#FAFAFA;">
            <div class="card-body">
                <div class="small fw-semibold mb-2" style="color:#4b3028;">Stock Status Legend:</div>
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle me-2" style="width:12px; height:12px; background:#C0392B;"></div>
                        <span class="small text-muted">Low Stock: ≤ Min. Stock</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle me-2" style="width:12px; height:12px; background:#D68910;"></div>
                        <span class="small text-muted">Medium: ≤ 1.5× Min. Stock</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle me-2" style="width:12px; height:12px; background:#2D7A3B;"></div>
                        <span class="small text-muted">Good: > 1.5× Min. Stock</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection