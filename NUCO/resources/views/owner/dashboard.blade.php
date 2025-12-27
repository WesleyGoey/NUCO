@extends('layouts.mainlayout')

@section('title', 'Owner Dashboard')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Owner Dashboard</h3>
            <div class="small text-muted">Overview & quick navigation</div>
        </div>
    </div>

    <div class="row g-3">
        @foreach($cards as $c)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                @php $href = $c['href'] ?? null; @endphp
                <a href="{{ $href ?? '#' }}" class="text-decoration-none {{ $href ? '' : 'disabled-link' }}"
                   {!! $href ? '' : 'onclick="return false;" aria-disabled="true"' !!}>
                    <div class="card h-100 shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <div class="fw-semibold" style="color:#4b3028;">{{ $c['title'] }}</div>
                                    <div class="small text-muted">{{ $c['desc'] }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color:#A4823B;font-size:1.1rem;">
                                        {{ $counts[$c['key']] ?? 0 }}
                                    </div>
                                    <div class="text-muted small">total</div>
                                </div>
                            </div>

                            <div class="mt-auto d-flex align-items-center justify-content-between">
                                <div class="text-muted small">
                                    <i class="bi {{ $c['icon'] }}" style="font-size:1.25rem;color:#A4823B;"></i>
                                </div>
                                <div>
                                    <i class="bi bi-arrow-right-circle" style="color:#A4823B;font-size:1.2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <style>.disabled-link { pointer-events: none; opacity: 0.6; }</style>
</div>
@endsection