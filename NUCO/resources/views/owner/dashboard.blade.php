@extends('layouts.mainlayout')

@section('title', 'Owner Dashboard')

@section('content')
<div class="container-xl py-4">
    {{-- ✅ SHOW ERROR MESSAGE IF EXISTS --}}
    @if(isset($error))
        <div class="alert alert-danger mb-4" role="alert">
            <h5 class="alert-heading">⚠️ Dashboard Error</h5>
            <p class="mb-0">{{ $error }}</p>
            <hr>
            <p class="mb-0 small">Check Railway logs for details.</p>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Owner Dashboard</h3>
            <div class="small text-muted">Overview & quick navigation</div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($cards as $c)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                @php $href = $c['href'] ?? null; @endphp
                <a href="{{ $href ?? '#' }}" class="text-decoration-none {{ $href ? '' : 'disabled-link' }}"
                   {!! $href ? '' : 'onclick="return false;" aria-disabled="true"' !!}>
                    <div class="card h-100 shadow-sm border-0 dashboard-card position-relative rounded-3" style="overflow:hidden; min-height:220px;">
                        <div class="card-body d-flex flex-column p-3">
                            {{-- Top: title --}}
                            <div class="mb-2">
                                <div class="fw-semibold text-dark">{{ $c['title'] }}</div>
                                <div class="small text-muted">{{ $c['desc'] }}</div>
                            </div>

                            {{-- Middle: centered count --}}
                            <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <div class="fw-bold" style="color:#A4823B; font-size:1.6rem;">
                                        {{ $counts[$c['key']] ?? 0 }}
                                    </div>
                                    <div class="small text-muted">total</div>
                                </div>
                            </div>

                            {{-- Bottom-right arrow --}}
                            <div class="mt-3 d-flex justify-content-end align-items-center">
                                <i class="bi bi-arrow-right-circle" style="color:#A4823B;font-size:1.2rem;"></i>
                            </div>

                            {{-- Icon: bottom-left using Bootstrap positioning --}}
                            <div class="position-absolute start-0 bottom-0 m-3">
                                <i class="bi {{ $c['icon'] }}" style="font-size:1.9rem;color:#A4823B;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    No data available to display. Please check back later.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection