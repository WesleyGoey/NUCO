@extends('layouts.mainlayout')

@section('title', 'Add User')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                <div class="card-header" style="background:#A4823B;padding:18px;">
                    <h4 class="m-0 fw-bold text-center" style="color:#F5F0E5;">Add New User</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="mx-auto" style="width:80px;height:80px;border-radius:12px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-person-plus-fill" style="font-size:2.5rem;color:#A4823B;"></i>
                        </div>
                        <div class="mt-2 fw-semibold" style="color:#4b3028;">Create Staff Account</div>
                    </div>

                    <form method="POST" action="{{ route('owner.users.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="username" class="form-label fw-bold" style="color:#4b3028;">Username</label>
                                <input id="username" name="username" type="text" 
                                       value="{{ old('username') }}" 
                                       class="form-control" required
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('username')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label fw-bold" style="color:#4b3028;">Phone</label>
                                <input id="phone" name="phone" type="text" 
                                       value="{{ old('phone') }}" 
                                       class="form-control" required
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('phone')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label fw-bold" style="color:#4b3028;">Email</label>
                                <input id="email" name="email" type="email" 
                                       value="{{ old('email') }}" 
                                       class="form-control" required
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('email')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label fw-bold" style="color:#4b3028;">Password</label>
                                <input id="password" name="password" type="password" 
                                       class="form-control" required
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('password')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold" style="color:#4b3028;">Confirm Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" 
                                       class="form-control" required
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="role" class="form-label fw-bold" style="color:#4b3028;">Role</label>
                                <select id="role" name="role" class="form-select" required
                                        style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                    <option value="">Select role</option>
                                    <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                                    <option value="waiter" {{ old('role') === 'waiter' ? 'selected' : '' }}>Waiter</option>
                                    <option value="chef" {{ old('role') === 'chef' ? 'selected' : '' }}>Chef</option>
                                    <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                                    <option value="reviewer" {{ old('role') === 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                                </select>
                                @error('role')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label fw-bold" style="color:#4b3028;">Status</label>
                                <select id="status" name="status" class="form-select" required
                                        style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="{{ route('owner.users') }}" class="btn btn-outline-secondary" 
                               style="padding:10px 24px;border-radius:10px;font-weight:600;">
                                Cancel
                            </a>
                            <button type="submit" class="btn" 
                                    style="background:#A4823B;color:#F5F0E5;padding:10px 24px;border-radius:10px;font-weight:700;">
                                <i class="bi bi-check-circle me-2"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection