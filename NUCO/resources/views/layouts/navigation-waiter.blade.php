<nav class="navbar navbar-expand-lg" style="background-color: #A4823B; box-shadow: 0 2px 4px rgba(0,0,0,0.25);">
    <div class="container-fluid position-relative" style="min-height:70px;">
        <div class="d-flex align-items-center justify-content-between" style="height:70px; width:100%;">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" style="margin-left:8px;">
                <span style="background:#F5F0E5;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name', 'NUCO') }}" style="height:40px;width:40px;object-fit:contain;">
                </span>
            </a>

            <div class="d-none d-lg-flex align-items-center ms-auto" style="gap:2rem;">
                <ul class="navbar-nav mb-0 d-flex flex-row align-items-center" style="gap:1.5rem;">
                    <li class="nav-item"><a class="nav-link fs-5" href="{{ route('waiter.tables') }}">Tables</a></li>
                    <li class="nav-item"><a class="nav-link fs-5" href="{{ route('menu') }}">Menu</a></li>
                    <li class="nav-item"><a class="nav-link fs-5" href="{{ route('discounts') }}">Discounts</a></li>
                    <li class="nav-item"><a class="nav-link fs-5" href="{{ route('waiter.orders') }}">Orders</a></li>
                </ul>

                <div class="d-flex align-items-center" style="gap:0.6rem;">
                    @auth
                        {{-- Desktop: person icon linking to profile --}}
                        <a href="{{ route('profile') }}" class="d-inline-flex align-items-center justify-content-center"
                           style="width:44px;height:44px;border-radius:50%;overflow:hidden;text-decoration:none;background:#F5F0E5;color:#A4823B;">
                            <i class="bi bi-person" style="font-size:1.25rem; color:#A4823B;"></i>
                        </a>
                    @endauth
                 </div>
             </div>
 
             <!-- MOBILE TOGGLER (right) -->
             <button class="navbar-toggler d-lg-none border-0 ms-auto" type="button" data-bs-toggle="collapse"
                 data-bs-target="#nucoNavbarWaiter" aria-controls="nucoNavbarWaiter" aria-expanded="false"
                 aria-label="Toggle navigation">
                 <i class="bi-list"></i>
             </button>
         </div>
 
         <!-- COLLAPSE: mobile vertical panel (pushes content) -->
         <div class="collapse navbar-collapse" id="nucoNavbarWaiter">
             <div class="w-100">
                 <ul class="navbar-nav d-lg-none flex-column" style="gap:0.6rem; padding:0.75rem 1rem; margin:0;">
                     <li class="nav-item"><a class="nav-link" href="{{ route('waiter.tables') }}">Tables</a></li>
                     <li class="nav-item"><a class="nav-link" href="{{ route('menu') }}">Menu</a></li>
                     <li class="nav-item"><a class="nav-link" href="{{ route('discounts') }}">Discounts</a></li>
                     <li class="nav-item"><a class="nav-link" href="{{ route('waiter.orders') }}">Orders</a></li>
 
                    @auth
                        {{-- mobile: only Profile link; logout lives in profile page --}}
                        <li class="nav-item"><a class="nav-link" href="{{ route('profile') }}">Profile</a></li>
                    @endauth
                 </ul>
             </div>
         </div>
     </div>
 
     <div style="height:8px;background:#4CAF50;border-radius:0 0 8px 8px;"></div>
 </nav>
 
 <!-- Cart UI moved out of navigation; use component @includeIf('components.waiter-cart') in layout when needed -->