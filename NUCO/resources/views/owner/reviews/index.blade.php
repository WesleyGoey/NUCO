@extends('layouts.mainlayout')

@section('title', 'Reviews')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold" style="color:#4b3028;">Customer Reviews</h3>
            <div class="small text-muted">Monitor customer feedback</div>
        </div>
        <div class="text-end">
            <div class="small text-muted">Total reviews</div>
            <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">{{ $reviews->total() }}</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:10px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0" style="border-radius:12px; border-left: 4px solid #FFD700;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Average Rating</div>
                            <div class="fw-bold" style="color:#FFD700; font-size:1.75rem;">
                                {{ number_format($stats['avgRating'], 1) }} <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                        <div style="font-size:2rem; color:#FFD700;">
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0" style="border-radius:12px; border-left: 4px solid #2D7A3B;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">5-Star Reviews</div>
                            <div class="fw-bold" style="color:#2D7A3B; font-size:1.75rem;">{{ $stats['rating5'] }}</div>
                        </div>
                        <div style="font-size:2rem; color:#2D7A3B;">
                            <i class="bi bi-emoji-smile-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0" style="border-radius:12px; border-left: 4px solid #C0392B;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Low Ratings (1-2 ⭐)</div>
                            <div class="fw-bold" style="color:#C0392B; font-size:1.75rem;">{{ $stats['rating1'] + $stats['rating2'] }}</div>
                        </div>
                        <div style="font-size:2rem; color:#C0392B;">
                            <i class="bi bi-emoji-frown-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews List --}}
    <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead style="background:#F5F0E5;">
                        <tr>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">#</th>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Rating</th>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Comment</th>
                            <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr style="border-bottom:1px solid #E9E6E2;">
                                <td class="px-4 py-3">{{ $review->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill" style="color:#FFD700; font-size:1.1rem;"></i>
                                            @else
                                                <i class="bi bi-star" style="color:#ddd; font-size:1.1rem;"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-2 fw-bold" style="color:#4b3028;">{{ $review->rating }}/5</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($review->comment)
                                        <div style="max-width:300px;">
                                            {{ Str::limit($review->comment, 80) }}
                                        </div>
                                    @else
                                        <span class="text-muted small">No comment</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div>{{ $review->created_at->format('d M Y') }}</div>
                                    <div class="small text-muted">{{ $review->created_at->format('H:i') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox" style="font-size:2rem;color:#E9E6E2;"></i>
                                    <div class="mt-2">No reviews found.</div>
                                </td>
                            </tr>
                        @endforelse
        </tbody>
    </table>
</div>

{{-- ✅ CUSTOM INLINE PAGINATION --}}
@if ($reviews->lastPage() > 1)
    <div class="d-flex justify-content-center mt-4">
        <nav>
            <ul class="pagination" style="gap:0.7rem; margin:0; padding:0; list-style:none; display:flex;">
                @if ($reviews->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; cursor:not-allowed; display:inline-block;">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $reviews->previousPageUrl() }}" rel="prev" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">&laquo;</a>
                    </li>
                @endif

                @foreach ($reviews->links()->elements[0] as $page => $url)
                    @if ($page == $reviews->currentPage())
                        <li class="page-item active">
                            <span class="page-link" style="color:#F5F0E5; background:#A4823B; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:700; display:inline-block; box-shadow:0 4px 12px rgba(164,130,59,0.3);">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                @if ($reviews->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $reviews->nextPageUrl() }}" rel="next" style="color:#A4823B; background:#F5F0E5; border-radius:18px; border:2px solid #A4823B; padding:8px 16px; font-weight:600; text-decoration:none; display:inline-block;">&raquo;</a>
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
</div>
@endsection