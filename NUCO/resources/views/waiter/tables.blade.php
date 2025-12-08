@extends('layouts.mainlayout')

@section('title', 'Tables')

@section('content')

<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0 fw-bold">Select Table</h3>
            <div class="small text-muted">Click a table to select it and start an order</div>
        </div>
        <div>
            @if($selectedId)
                <div class="small text-muted">Selected table: <strong>{{ session('selected_table.table_number') }}</strong></div>
            @endif
        </div>
    </div>

    <div class="row g-3">
        @forelse($tables as $table)
            @php
                $isAvailable = ($table->status ?? 'available') === 'available';
                $isSelected = (int)$selectedId === (int)$table->id;
            @endphp

            <div class="col-6 col-sm-4 col-md-3">
                <form method="POST" action="{{ route('waiter.tables.select') }}" class="h-100">
                    @csrf
                    <input type="hidden" name="table_id" value="{{ $table->id }}">
                    <button type="submit"
                        class="w-100 border-0 p-0 bg-transparent text-start"
                        @if(!$isAvailable) disabled aria-disabled="true" title="Table not available" @endif
                        style="cursor: {{ $isAvailable ? 'pointer' : 'not-allowed' }};">
                        <div class="card h-100 position-relative text-center"
                             style="
                                border-radius:14px;
                                padding:20px;
                                min-height:150px;
                                transition: transform .12s ease, box-shadow .12s ease;
                                {{ $isSelected ? 'transform: translateY(-6px); box-shadow: 0 12px 34px rgba(0,0,0,0.14);' : 'box-shadow: 0 6px 18px rgba(0,0,0,0.06);' }}
                                {{ $isAvailable ? 'background:#FFFFFF;' : 'background:#FFF3E6; border:1px solid rgba(196,122,26,0.12);' }}
                             ">
                            <div class="mb-2">
                                <div style="display:inline-block;padding:8px 14px;border-radius:999px;background:#F5F0E5;color:#A4823B;font-weight:700;">
                                    Table {{ $table->table_number }}
                                </div>
                            </div>

                            <div class="small text-muted mb-1">Capacity</div>
                            <div class="fw-bold" style="font-size:1.15rem;">{{ $table->capacity }}</div>

                            <div class="mt-3">
                                @if($isAvailable)
                                    <span class="badge rounded-pill" style="background:#E6F9EE;color:#2D7A3B;padding:6px 10px;">Available</span>
                                @else
                                    <span class="badge rounded-pill" style="background:#FFECEC;color:#C0392B;padding:6px 10px;">Occupied</span>
                                @endif
                            </div>

                            @if($isSelected)
                                <div class="position-absolute" style="right:12px; top:12px;">
                                    <i class="bi bi-check-circle-fill" style="color:#2D7A3B; font-size:1.1rem;"></i>
                                </div>
                            @endif

                            @if(!$isAvailable)
                                <div class="position-absolute w-100 h-100" style="left:0;top:0;border-radius:14px;background:rgba(255,255,255,0.6);"></div>
                            @endif
                        </div>
                    </button>
                </form>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-4">No tables configured.</div>
            </div>
        @endforelse
    </div>
</div>

<style>
/* small responsive tweak */
@media (max-width: 575.98px) {
    .card { padding:16px !important; min-height:130px !important; }
}
</style>
@endsection