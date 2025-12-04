<nav class="navbar navbar-expand-lg" style="background-color: #B8860B; box-shadow: 0 2px 4px rgba(0,0,0,0.25);">
    <div class="container-fluid position-relative" style="min-height:70px;">
        <div class="d-flex align-items-center" style="height:70px;">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" style="margin-left:8px;">
                <span
                    style="background:#F8F8F4;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        style="height:40px;width:40px;object-fit:contain;">
                </span>
                <span class="ms-2 fw-bold" style="color:#000000; font-size:1.05rem;">{{ config('app.name', 'NUCO') }}</span>
            </a>

            <button class="navbar-toggler ms-3 border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#nucoNavbar" aria-controls="nucoNavbar" aria-expanded="false"
                aria-label="Toggle navigation" style="color:#000000;">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="nucoNavbar">
            <div class="w-100 d-flex flex-column align-items-lg-center justify-content-lg-center" style="margin-right:50px;">
                <ul class="navbar-nav mb-2 mb-lg-0 mx-auto d-flex flex-row" style="gap:2rem;">
                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 {{ request()->is('menu*') ? 'active' : '' }}"
                            href="{{ route('menu') ?? url('/menu') }}"
                            style="color:#000000;">Menu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 {{ request()->is('discounts*') ? 'active' : '' }}"
                            href="{{ route('discounts') ?? url('/discounts') }}"
                            style="color:#000000;">Discounts</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end"
            style="height:70px;position:absolute;top:0;right:0;z-index:2;margin-right:20px;">
            <ul class="navbar-nav flex-row" style="gap:1.2rem;">
                @auth
                    <li class="nav-item">
                        <a class="fs-5" href="{{ route('cart') ?? url('/cart') }}" title="Cart" style="color:#28A745;">
                            <span style="background:#F8F8F4;border-radius:50%;width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                                <i class="bi bi-cart" style="color:#000000;"></i>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="fs-5" href="{{ route('profile.edit') ?? url('/profile') }}" title="Profile" style="color:#28A745;">
                            <span style="background:#F8F8F4;border-radius:50%;width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                                <i class="bi bi-person-circle" style="color:#000000;"></i>
                            </span>
                        </a>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a class="btn fw-bold px-4 py-2 d-inline-block" href="{{ route('login') }}"
                            style="background:#F8F8F4;color:#B8860B;border-radius:28px;box-shadow:0 2px 12px rgba(0,0,0,0.08);font-weight:700;">
                            Login
                        </a>
                    </li>
                    @if(Route::has('register'))
                        <li class="nav-item">
                            <a class="btn fw-bold px-4 py-2 d-inline-block" href="{{ route('register') }}"
                                style="background:transparent;color:#F8F8F4;border:2px solid #F8F8F4;border-radius:28px;box-shadow:0 2px 6px rgba(0,0,0,0.06);">
                                Register
                            </a>
                        </li>
                    @endif
                @endguest
            </ul>
        </div>
    </div>

    <div style="height:8px;background:#28A745;border-radius:0 0 8px 8px;"></div>
</nav>

<style>
    /* Palette: #B8860B, #000000, #F8F8F4, #28A745, #DC3545 */
    .nav-link { transition: all .12s ease-in-out; }
    .nav-link:hover { color: #000000 !important; transform: translateY(-2px); }
    .navbar .active { text-decoration: underline; text-underline-offset:6px; color:#000000 !important; }
</style>
```// filepath: resources/views/layouts/navigation.blade.php
<nav class="navbar navbar-expand-lg" style="background-color: #B8860B; box-shadow: 0 2px 4px rgba(0,0,0,0.25);">
    <div class="container-fluid position-relative" style="min-height:70px;">
        <div class="d-flex align-items-center" style="height:70px;">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" style="margin-left:8px;">
                <span
                    style="background:#F8F8F4;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        style="height:40px;width:40px;object-fit:contain;">
                </span>
                <span class="ms-2 fw-bold" style="color:#000000; font-size:1.05rem;">{{ config('app.name', 'NUCO') }}</span>
            </a>

            <button class="navbar-toggler ms-3 border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#nucoNavbar" aria-controls="nucoNavbar" aria-expanded="false"
                aria-label="Toggle navigation" style="color:#000000;">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="nucoNavbar">
            <div class="w-100 d-flex flex-column align-items-lg-center justify-content-lg-center" style="margin-right:50px;">
                <ul class="navbar-nav mb-2 mb-lg-0 mx-auto d-flex flex-row" style="gap:2rem;">
                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 {{ request()->is('menu*') ? 'active' : '' }}"
                            href="{{ route('menu') ?? url('/menu') }}"
                            style="color:#000000;">Menu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 {{ request()->is('discounts*') ? 'active' : '' }}"
                            href="{{ route('discounts') ?? url('/discounts') }}"
                            style="color:#000000;">Discounts</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end"
            style="height:70px;position:absolute;top:0;right:0;z-index:2;margin-right:20px;">
            <ul class="navbar-nav flex-row" style="gap:1.2rem;">
                @auth
                    <li class="nav-item">
                        <a class="fs-5" href="{{ route('cart') ?? url('/cart') }}" title="Cart" style="color:#28A745;">
                            <span style="background:#F8F8F4;border-radius:50%;width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                                <i class="bi bi-cart" style="color:#000000;"></i>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="fs-5" href="{{ route('profile.edit') ?? url('/profile') }}" title="Profile" style="color:#28A745;">
                            <span style="background:#F8F8F4;border-radius:50%;width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                                <i class="bi bi-person-circle" style="color:#000000;"></i>
                            </span>
                        </a>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a class="btn fw-bold px-4 py-2 d-inline-block" href="{{ route('login') }}"
                            style="background:#F8F8F4;color:#B8860B;border-radius:28px;box-shadow:0 2px 12px rgba(0,0,0,0.08);font-weight:700;">
                            Login
                        </a>
                    </li>
                    @if(Route::has('register'))
                        <li class="nav-item">
                            <a class="btn fw-bold px-4 py-2 d-inline-block" href="{{ route('register') }}"
                                style="background:transparent;color:#F8F8F4;border:2px solid #F8F8F4;border-radius:28px;box-shadow:0 2px 6px rgba(0,0,0,0.06);">
                                Register
                            </a>
                        </li>
                    @endif
                @endguest
            </ul>
        </div>
    </div>

    <div style="height:8px;background:#28A745;border-radius:0 0 8px 8px;"></div>
</nav>

<style>
    /* Palette: #B8860B, #000000, #F8F8F4, #28A745, #DC3545 */
    .nav-link { transition: all .12s ease-in-out; }
    .nav-link:hover { color: #000000 !important; transform: translateY(-2px); }
    .navbar .active { text-decoration: underline; text-underline-offset:6px; color:#000000 !important; }
</style>