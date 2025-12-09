@extends('layouts.mainlayout')

@section('title', 'Reviews')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">Reviews</h3>
                    <p class="text-muted mb-4">This page is for reviewer users. Add review management UI here.</p>

                    {{-- Example: list placeholder --}}
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-star" style="font-size:2rem;"></i>
                        <div class="mt-2">No UI yet — implement review list / moderation here.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection