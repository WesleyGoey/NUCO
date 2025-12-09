@extends('layouts.mainlayout')

@section('title', 'Thank You')

@section('content')
<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <div class="mx-auto d-flex align-items-center justify-content-center" 
                             style="width:120px; height:120px; background:linear-gradient(135deg, #E6F9EE 0%, #C8F0DC 100%); border-radius:50%; box-shadow:0 8px 24px rgba(45,122,59,0.12);">
                            <i class="bi bi-check-circle-fill" style="font-size:4.5rem; color:#2D7A3B;"></i>
                        </div>
                    </div>
                    
                    <h2 class="fw-bold mb-3" style="color:#4b3028; font-size:2rem;">Thank You!</h2>
                    <p class="text-muted mb-4" style="font-size:1.1rem; line-height:1.6;">
                        Your feedback has been submitted successfully.<br>
                        We appreciate you taking the time to share your experience with us.
                    </p>
                    
                    <p class="text-muted mb-4" style="font-size:0.95rem;">
                        Redirecting in <span id="countdown" class="fw-bold" style="color:#A4823B;">5</span> seconds...
                    </p>
                    
                    <div class="d-grid">
                        <a href="{{ route('reviewer.reviews') }}" class="btn btn-lg" 
                           style="background:#A4823B;color:#F5F0E5;border:none;border-radius:12px;padding:14px;font-weight:700;font-size:1rem;box-shadow:0 4px 12px rgba(164,130,59,0.2);">
                            <i class="bi bi-arrow-left-circle me-2"></i> Submit Another Review Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let seconds = 5;
    const countdownElement = document.getElementById('countdown');
    
    // Update countdown every second
    const interval = setInterval(function() {
        seconds--;
        countdownElement.textContent = seconds;
        
        if (seconds <= 0) {
            clearInterval(interval);
            // Redirect to reviews page
            window.location.href = "{{ route('reviewer.reviews') }}";
        }
    }, 1000);
});
</script>
@endsection