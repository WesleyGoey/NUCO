<nav class="navbar navbar-expand-lg" style="background-color: #A4823B; box-shadow: 0 2px 4px rgba(0,0,0,0.12);">
    <div class="container-fluid" style="min-height:70px;">
        <div class="d-flex align-items-center justify-content-between" style="width:100%; height:70px;">
            <!-- Left: Brand / Logo -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('reviewer.reviews') }}" style="margin-left:8px;">
                <span style="background:#F5F0E5;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name', 'NUCO') }}" style="height:40px;width:40px;object-fit:contain;">
                </span>
            </a>

            <!-- Right: Profile icon -->
            <div class="d-flex align-items-center" style="margin-right:8px;">
                @auth
                    <a href="{{ route('profile') }}" aria-label="Profile" title="Profile"
                       style="width:44px;height:44px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#F5F0E5;color:#A4823B;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                        <i class="bi bi-person-circle" style="font-size:1.5rem;color:#A4823B;"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" aria-label="Login" title="Login"
                       style="width:44px;height:44px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#F5F0E5;color:#A4823B;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                        <i class="bi bi-box-arrow-in-right" style="font-size:1.15rem;color:#A4823B;"></i>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
