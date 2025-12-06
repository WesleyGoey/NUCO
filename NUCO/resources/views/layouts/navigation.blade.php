<nav class="navbar navbar-expand-lg" style="background-color: #A4823B; box-shadow: 0 2px 4px rgba(0,0,0,0.25);">
    <div class="container-fluid position-relative" style="min-height:70px;">
        <div class="d-flex align-items-center" style="height:70px;">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" style="margin-left:8px;">
                <span style="background:#F5F0E5;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name', 'NUCO') }}" style="height:40px;width:40px;object-fit:contain;">
                </span>
            </a>
        </div>

        <!-- RIGHT: icon-only hamburger -->
        <div class="d-flex align-items-center ms-auto">
            <button class="btn p-0 d-flex align-items-center justify-content-center" 
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#nucoNavbar"
                    aria-controls="nucoNavbar"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                    style="background:transparent;border:none;color:#ffffff;width:44px;height:44px;">
                <i class="bi-list" style="font-size:1.25rem;"></i>
            </button>
        </div>

        <!-- collapse placed in normal flow so it pushes content down when open -->
        <div class="collapse w-100" id="nucoNavbar">
            <div class="card shadow-sm w-100" style="border-radius:0 0 12px 12px; overflow:hidden; background:#A4823B; border: none;">
                <div class="card-body p-3">
                    <ul class="navbar-nav mb-2 mb-lg-0 d-flex flex-column" style="gap:0.4rem;">
                        <li class="nav-item">
                            <a class="nav-link fs-5" href="{{ route('menu') }}" style="color:#F5F0E5; text-decoration:none;">Menu</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link fs-5" href="{{ route('discounts') }}" style="color:#F5F0E5; text-decoration:none;">Discounts</a>
                        </li>

                        {{-- Guest: show only Login (no Register) --}}
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}" style="color:#F5F0E5; text-decoration:none;">Login</a>
                            </li>
                        @else
                            {{-- Authenticated users: Dashboard + Logout --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" style="color:#F5F0E5; text-decoration:none;">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm w-100" style="background:#F5F0E5;color:#A4823B;border-radius:8px;border:none;">Logout</button>
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </div>
     </div>
</nav>

<!-- Inline CSS: normalize nav-link font size & remove borders -->
<style>
    /* Make all hamburger/collapse nav items use the same font size and normal weight */
    .navbar .nav-link {
        font-size: 1rem !important;
        font-weight: 400 !important;
        color: #F5F0E5 !important;
        border: none !important;
        padding-left: 0 !important;
    }

    /* Remove any default borders and outlines inside collapse card */
    #nucoNavbar .card, #nucoNavbar .card-body {
        border: none !important;
        box-shadow: none !important;
        background: #A4823B !important;
    }

    /* Hover underline only (no color change) */
    .navbar .nav-link:hover {
        text-decoration: underline !important;
        text-decoration-thickness: 2px;
        text-underline-offset: 6px;
    }
</style>