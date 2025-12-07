@extends('layouts.mainlayout')

@section('title', 'Profile')

@section('content')
<div class="container-xl py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div style="width:100px;height:100px;border-radius:16px;display:flex;align-items:center;justify-content:center;background:#F5F0E5;flex-shrink:0;">
                            <i class="bi bi-person-fill" style="font-size:36px;color:#A4823B;"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h3 class="mb-1 fw-bold" style="font-size:1.5rem;">{{ $user->username ?? '-' }}</h3>
                            <div class="small text-muted" style="font-size:0.95rem;">{{ $user->email ?? '-' }}</div>
                        </div>

                        {{-- status badge --}}
                        <div class="text-end">
                            @php $isActive = ($user->status ?? '') === 'active'; @endphp
                            <span class="badge rounded-pill px-3 py-2"
                                  style="background: {{ $isActive ? '#E6F9EE' : '#FFECEC' }}; color: {{ $isActive ? '#2D7A3B' : '#C0392B' }}; font-weight:700;">
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="row gy-3" style="font-size:1rem;">
                        <div class="col-12 col-md-6">
                            <div class="small text-muted mb-1">Username</div>
                            <div class="fw-semibold">{{ $user->username ?? '-' }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="small text-muted mb-1">Phone</div>
                            <div class="fw-semibold">{{ $user->phone ?? '-' }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="small text-muted mb-1">Role</div>
                            <div class="fw-semibold text-capitalize">{{ $user->role ?? '-' }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="small text-muted mb-1">Joined</div>
                            <div class="fw-semibold">{{ optional($user->created_at)->format('d M Y') ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn"
                           style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 18px;font-weight:600;">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary"
                                    style="padding:10px 18px;border-radius:10px;font-weight:600;">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection