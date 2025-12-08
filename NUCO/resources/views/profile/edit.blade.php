@extends('layouts.mainlayout')

@section('title', 'Edit Profile')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card" style="border-radius:14px; overflow:hidden; border:1px solid rgba(164,130,59,0.06); box-shadow:0 10px 28px rgba(0,0,0,0.06);">
                    <div style="background:#A4823B; padding:14px 18px;">
                        <h2 class="m-0 fw-bold text-center" style="color:#F5F0E5; font-size:1.15rem;">Edit Profile</h2>
                    </div>

                    <div class="p-4" style="background:transparent;">
                        {{-- Centered avatar + username --}}
                        <div class="text-center mb-4">
                            <div class="mx-auto" style="width:88px;height:88px;border-radius:12px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(0,0,0,0.04);">
                                <i class="bi bi-person-fill" style="font-size:32px;color:#A4823B"></i>
                            </div>
                            <div class="mt-3 fw-semibold" style="color:#4b3028;font-size:1rem;">
                                {{ $user->username ?? $user->email }}
                            </div>
                        </div>

                        {{-- Form partial --}}
                        @include('profile.partials.update-profile-information-form', ['user' => $user ?? auth()->user()])

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
