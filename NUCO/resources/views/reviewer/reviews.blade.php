@extends('layouts.mainlayout')

@section('title', 'Submit Review')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header text-center" style="background:#A4823B; padding:18px;">
                    <h4 class="m-0 fw-bold" style="color:#F5F0E5;">We'd Love Your Feedback!</h4>
                    <p class="m-0 mt-1 small" style="color:#F5F0E5; opacity:0.9;">Help us improve by sharing your experience</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('reviewer.reviews.store') }}" id="reviewForm">
                        @csrf

                        {{-- Star Rating --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-center d-block mb-3" style="color:#4b3028; font-size:1rem;">
                                How was your experience?
                            </label>
                            
                            <input type="hidden" name="rating" id="ratingInput" value="" required>
                            
                            <div class="d-flex justify-content-center gap-2" id="starRating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star star-icon" 
                                       data-rating="{{ $i }}"
                                       style="font-size:2.5rem; color:#ddd; cursor:pointer; transition: transform 0.1s ease;">
                                    </i>
                                @endfor
                            </div>
                            
                            @error('rating')
                                <div class="text-danger text-center mt-2 small">{{ $message }}</div>
                            @enderror
                            
                            <div id="ratingText" class="text-center mt-2 fw-bold" style="color:#A4823B; font-size:1rem; min-height:26px;"></div>
                        </div>

                        {{-- Comment --}}
                        <div class="mb-3">
                            <label for="comment" class="form-label fw-bold" style="color:#4b3028; font-size:1rem;">
                                Tell us more (optional)
                            </label>
                            <textarea name="comment" id="comment" rows="4" 
                                      class="form-control" 
                                      placeholder="Share your thoughts with us..."
                                      style="border-radius:10px; border:2px solid #E9E6E2; padding:12px; font-size:0.95rem;">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg" 
                                    style="background:#A4823B;color:#F5F0E5;border:none;border-radius:10px;padding:12px;font-weight:700;font-size:1rem;">
                                <i class="bi bi-send me-2"></i> Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .star-icon.active {
        color: #FFD700 !important;
        transform: scale(1.05);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-icon');
    const ratingInput = document.getElementById('ratingInput');
    const ratingText = document.getElementById('ratingText');
    
    const ratingLabels = {
        1: '😞 Poor',
        2: '😕 Fair',
        3: '😐 Good',
        4: '😊 Very Good',
        5: '😍 Excellent'
    };
    
    let selectedRating = 0;
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            selectedRating = rating;
            ratingInput.value = rating;
            
            // Update stars - only clicked stars, no hover effect
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill', 'active');
                    s.style.color = '#FFD700';
                } else {
                    s.classList.remove('bi-star-fill', 'active');
                    s.classList.add('bi-star');
                    s.style.color = '#ddd';
                }
            });
            
            // Update text
            ratingText.textContent = ratingLabels[rating];
        });
    });
});
</script>
@endsection