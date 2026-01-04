@extends('layouts.mainlayout')

@section('title', 'Discounts')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Active Discounts</h1>
        <small class="text-muted">Latest promos</small>
    </div>

    <div class="d-flex flex-column gap-3">
        @forelse($discounts as $discount)
            @php
                $isAmount = $discount->type === 'amount';
                $valueNumber = $isAmount ? number_format($discount->value, 0, ',', '.') : $discount->value;
                $valueLabel = $isAmount ? $valueNumber : "{$discount->value}%";
                $firstPeriod = $discount->periods->first();
                $periodLabel = $firstPeriod
                    ? ($firstPeriod->end_date ? "{$firstPeriod->start_date} — {$firstPeriod->end_date}" : "{$firstPeriod->start_date} — ongoing")
                    : null;
                
                $img = $discount->image_path ? asset('storage/' . $discount->image_path) : null;
                $initial = strtoupper(substr($discount->name, 0, 1));
            @endphp

            <div class="card rounded-3 shadow-sm overflow-hidden border-0">
                <div class="row g-0 align-items-stretch">
                    <div class="col-12 col-md-3">
                        @if($img)
                            <div class="ratio ratio-4x3">
                                <img src="{{ $img }}" 
                                     alt="{{ $discount->name }}"
                                     style="width:100%; height:100%; object-fit:cover; display:block;">
                            </div>
                        @else
                            {{-- ✅ FINAL: 16px icon (much smaller!) --}}
                            <div class="ratio ratio-4x3 d-flex align-items-center justify-content-center" 
                                 style="background:#F5F0E5;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A4823B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                    <circle cx="7" cy="7" r="1.5" fill="#A4823B"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="col-12 col-md-9">
                        <div class="p-4 d-flex flex-column h-100">
                            <div class="d-flex">
                                <div class="flex-grow-1 pe-3">
                                    <h5 class="fw-bold mb-2 text-truncate">{{ $discount->name }}</h5>

                                    <div class="text-muted small mb-2">
                                        @if($isAmount)
                                            Rp {{ $valueNumber }} off
                                        @else
                                            {{ $discount->value }}% off
                                        @endif

                                        @if($discount->min_order_amount)
                                            • min Rp {{ number_format($discount->min_order_amount,0,',','.') }}
                                        @endif
                                    </div>

                                    @if($periodLabel)
                                        <div class="text-muted small">Period: {{ $periodLabel }}</div>
                                    @endif

                                    @if($firstPeriod && !empty($firstPeriod->description))
                                        <p class="mt-3 mb-0 text-muted" style="line-height:1.4;">{{ $firstPeriod->description }}</p>
                                    @endif
                                </div>

                                <div class="d-flex align-items-start">
                                    <div class="bg-white border rounded-3 text-center p-3 ms-3" 
                                         style="width:120px; border-color: rgba(164,130,59,0.12); box-shadow: 0 8px 20px rgba(0,0,0,0.04);">
                                        @if($isAmount)
                                            <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">{{ $valueNumber }}</div>
                                            <div class="small text-muted mt-1">FLAT</div>
                                        @else
                                            <div class="fw-bold" style="color:#A4823B; font-size:1.35rem;">{{ $discount->value }}%</div>
                                            <div class="small text-muted mt-1">OFF</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-3">
                                <div class="rounded" 
                                     style="height:10px; background: linear-gradient(90deg,#A4823B 0%,#D6BB7F 100%); box-shadow:0 6px 16px rgba(164,130,59,0.12);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-tag" style="font-size:3rem; color:#E9E6E2;"></i>
                <div class="mt-3">No active discounts at the moment.</div>
            </div>
        @endforelse
    </div>

    {{-- ✅ CUSTOM INLINE PAGINATION --}}
    @if ($discounts->lastPage() > 1)
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination" style="gap:0.7rem; margin:0; padding:0; list-style:none; display:flex;">
                    @if ($discounts->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; cursor:not-allowed; display:inline-block;">&laquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $discounts->previousPageUrl() }}" rel="prev" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block; transition:all 0.2s;">&laquo;</a>
                        </li>
                    @endif

                    @foreach ($discounts->links()->elements[0] as $page => $url)
                        @if ($page == $discounts->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="color:#F5F0E5; background:#A4823B; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:700; display:inline-block; box-shadow:0 4px 12px rgba(164,130,59,0.3);">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block; transition:all 0.2s;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if ($discounts->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $discounts->nextPageUrl() }}" rel="next" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block; transition:all 0.2s;">&raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; cursor:not-allowed; display:inline-block;">&raquo;</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
</div>

<style>
@media (max-width: 767.98px) {
    .card .p-4 { 
        padding: 1rem !important; 
    }
    .card .bg-white { 
        width:100% !important; 
        margin-top:0.75rem; 
    }
}
</style>
@endsection