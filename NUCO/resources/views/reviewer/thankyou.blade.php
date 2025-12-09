@extends('layouts.mainlayout')

@section('title', 'Thank You')

@section('content')
<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden text-center">
                <div class="card-body p-5">
                    {{-- Success Icon --}}
                    <div class="mb-4">
                        <div class="mx-auto d-flex align-items-center justify-content-center" 
                             style="width:120px; height:120px; background:linear-gradient(135deg, #E6F9EE 0%, #C8F0DC 100%); border-radius:50%; box-shadow:0 8px 24px rgba(45,122,59,0.12);">
                            <i class="bi bi-check-circle-fill" style="font-size:4.5rem; color:#2D7A3B;"></i>
                        </div>
                    </div>
                    
                    {{-- Thank You Message --}}
                    <h2 class="fw-bold mb-3" style="color:#4b3028; font-size:2rem;">Thank You!</h2>
                    <p class="text-muted mb-4" style="font-size:1.1rem; line-height:1.6;">
                        Your feedback has been submitted successfully.<br>
                        We appreciate you taking the time to share your experience with us.
                    </p>
                    
                    {{-- Decorative Stars --}}
                    <div class="mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star-fill" style="font-size:1.5rem; color:#FFD700; margin:0 4px;"></i>
                        @endfor
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        <a href="{{ route('reviewer.reviews') }}" class="btn btn-lg" 
                           style="background:#A4823B;color:#F5F0E5;border:none;border-radius:12px;padding:14px;font-weight:700;font-size:1rem;box-shadow:0 4px 12px rgba(164,130,59,0.2);">
                            <i class="bi bi-arrow-left-circle me-2"></i> Submit Another Review
                        </a>
                        
                        <a href="{{ route('profile') }}" class="btn btn-lg btn-outline-secondary" 
                           style="border:2px solid #E9E6E2;border-radius:12px;padding:14px;font-weight:600;color:#6b6b6b;">
                            <i class="bi bi-person me-2"></i> Go to Profile
                        </a>
                    </div>

                    {{-- Optional: Logout Button --}}
                    <div class="mt-4">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted" 
                                    style="text-decoration:none; font-size:0.9rem;">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Optional: Fun Fact or Quote --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-4" style="background:linear-gradient(135deg, #FFF8E6 0%, #FFECB3 100%);">
                <div class="card-body p-4 text-center">
                    <div class="mb-2">
                        <i class="bi bi-lightbulb-fill" style="font-size:2rem; color:#F57C00;"></i>
                    </div>
                    <p class="fw-bold mb-1" style="color:#4b3028; font-size:1rem;">Did you know?</p>
                    <p class="text-muted small mb-0" style="line-height:1.5;">
                        Your feedback helps us improve our service and create better experiences for all our customers. Thank you for being part of our journey!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection