<nav class="navbar navbar-expand-lg" style="background-color: #A4823B; box-shadow: 0 2px 4px rgba(0,0,0,0.25);">
    <div class="container-fluid position-relative" style="min-height:70px;">
        <div class="d-flex align-items-center" style="height:70px;">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" style="margin-left:8px;">
                <span style="background:#F5F0E5;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name', 'NUCO') }}" style="height:40px;width:40px;object-fit:contain;">
                </span>
            </a>

            <button class="navbar-toggler ms-auto border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#nucoNavbar" aria-controls="nucoNavbar" aria-expanded="false"
                aria-label="Toggle navigation" style="color:#000000;">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="nucoNavbar">
            <div class="w-100 d-flex justify-content-end align-items-center" style="gap:2rem;">
                <ul class="navbar-nav mb-2 mb-lg-0 d-flex flex-row align-items-center" style="gap:1.5rem;">
                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5" href="{{ route('menu') ?? url('/menu') }}" style="color:#000000; text-decoration:none;">Menu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5" href="{{ route('discounts') ?? url('/discounts') }}" style="color:#000000; text-decoration:none;">Discounts</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center" style="gap:0.6rem;">
                    @guest
                        <a class="btn fw-bold px-3 py-2" href="{{ route('login') }}"
                            style="background:#F5F0E5;color:#A4823B;border-radius:20px;box-shadow:0 2px 8px rgba(0,0,0,0.06); text-decoration:none;">Login</a>
                    @else
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="background:#F5F0E5;color:#A4823B;border-radius:20px;border:none;">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <div style="height:8px;background:#4CAF50;border-radius:0 0 8px 8px;"></div>
</nav>

<!-- Inline CSS: underline on hover only, keep text color -->
<style>
    /* No animation, underline only */
    .nav-link { color: #000000 !important; text-decoration: none !important; }
    .nav-link:hover,
    .navbar .active {
        text-decoration: underline !important;
        text-decoration-thickness: 2px;
        text-underline-offset: 6px;
        color: #000000 !important;
    }
</style>