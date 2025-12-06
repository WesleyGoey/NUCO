@extends('layouts.mainlayout')

@section('title', 'Discounts')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Discounts</h1>
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
                $img = $discount->image_path ? $discount->image_path : null;
                $initial = strtoupper(substr($discount->name, 0, 1));
            @endphp

            <div class="card rounded-3 shadow-sm overflow-hidden border-0">
                <div class="row g-0 align-items-stretch">
                    {{-- LEFT: image (flush) --}}
                    @if($img)
                        <div class="col-12 col-md-4" style="min-height:160px; background-image:url('{{ $img }}'); background-size:cover; background-position:center;"></div>
                    @else
                        <div class="col-12 col-md-4 d-flex align-items-center justify-content-center" style="min-height:160px; background:#F5F0E5;">
                            <span class="fw-bold text-dark" style="font-size:48px;">{{ $initial }}</span>
                        </div>
                    @endif

                    {{-- RIGHT: content --}}
                    <div class="col-12 col-md-8">
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

                                    @if(!empty($discount->description))
                                        <p class="mt-3 mb-0 text-muted" style="line-height:1.4;">{{ $discount->description }}</p>
                                    @endif
                                </div>

                                {{-- VALUE BOX --}}
                                <div class="d-flex align-items-start">
                                    <div class="bg-white border rounded-3 text-center p-3 ms-3" style="width:120px; border-color: rgba(164,130,59,0.12); box-shadow: 0 8px 20px rgba(0,0,0,0.04);">
                                        @if($isAmount)
                                            {{-- two-line layout: big "Rp 5.000" + small "FLAT" --}}
                                            <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">{{ $valueNumber }}</div>
                                            <div class="small text-muted mt-1">FLAT</div>
                                        @else
                                            {{-- percent: big value + small OFF --}}
                                            <div class="fw-bold" style="color:#A4823B; font-size:1.35rem;">{{ $discount->value }}%</div>
                                            <div class="small text-muted mt-1">OFF</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- RIBBON / LINE starts from edge of content (right column) --}}
                            <div class="mt-auto pt-3">
                                <div class="rounded" style="height:10px; background: linear-gradient(90deg,#A4823B 0%,#D6BB7F 100%); box-shadow:0 6px 16px rgba(164,130,59,0.12);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">No discounts available</div>
        @endforelse
    </div>
</div>

{{-- responsive tweak: make value box full-width below text on small screens --}}
<style>
@media (max-width: 767.98px) {
    .card .p-4 { padding: 1rem; }
    .card .bg-white { width:100% !important; margin-top:0.75rem; }
}
</style>
@endsection