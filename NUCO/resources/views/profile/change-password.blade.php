@extends('layouts.mainlayout')

@section('title', 'Change Password')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card" style="border-radius:12px; overflow:hidden; border:1px solid rgba(164,130,59,0.06); box-shadow:0 10px 28px rgba(0,0,0,0.06);">
                    <div style="background:#A4823B; padding:12px 16px;">
                        <h2 class="m-0 fw-bold text-center" style="color:#F5F0E5; font-size:1.125rem;">Change Password</h2>
                    </div>

                    <div class="p-4" style="background:transparent;">
                        <div class="text-center mb-3">
                            <div class="mx-auto" style="width:72px;height:72px;border-radius:10px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(0,0,0,0.04);">
                                <i class="bi bi-shield-lock-fill" style="font-size:26px;color:#A4823B"></i>
                            </div>
                            <div class="mt-3 fw-semibold" style="color:#4b3028;font-size:1rem;">
                                {{ $user->username ?? $user->email }}
                            </div>
                        </div>

                        {{-- include the partial so form validation and naming stay identical --}}
                        @includeIf('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
