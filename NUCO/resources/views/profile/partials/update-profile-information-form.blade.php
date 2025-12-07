@php
    $u = $user ?? auth()->user();
@endphp

<section aria-labelledby="profile-info-heading">
    <header class="mb-3">
        <h3 id="profile-info-heading" class="h6 fw-bold mb-1" style="color:#4b3028;">Profile Information</h3>
        <p class="small text-muted mb-0">Edit your username, email and phone.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-3" novalidate>
        @csrf
        @method('patch')

        @if (session('status') === 'profile-updated')
            <div class="alert mb-3 py-2" role="alert" style="background:#EAF3EA;border:1px solid rgba(164,130,59,0.06);color:#2D7A3B;">
                Profile updated!
            </div>
        @endif

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="username" class="form-label fw-semibold" style="color:#4b3028;">Username</label>
                <input id="username" name="username" type="text"
                    value="{{ old('username', $u->username) }}" required autofocus autocomplete="username"
                    class="form-control"
                    style="height:48px; border-radius:10px; border:1px solid rgba(164,130,59,0.12); padding:0.6rem 0.9rem; background:#fff;">
                @error('username') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="phone" class="form-label fw-semibold" style="color:#4b3028;">Phone</label>
                <input id="phone" name="phone" type="text"
                    value="{{ old('phone', $u->phone) }}" autocomplete="tel"
                    class="form-control"
                    style="height:48px; border-radius:10px; border:1px solid rgba(164,130,59,0.12); padding:0.6rem 0.9rem; background:#fff;">
                @error('phone') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="email" class="form-label fw-semibold" style="color:#4b3028;">Email</label>
                <input id="email" name="email" type="email"
                    value="{{ old('email', $u->email) }}" required autocomplete="username"
                    class="form-control"
                    style="height:48px; border-radius:10px; border:1px solid rgba(164,130,59,0.12); padding:0.6rem 0.9rem; background:#fff;">
                @error('email') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="text-center my-3">
            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#changePasswordModal"
               class="fw-semibold" style="color:#A4823B;text-decoration:underline;">Change password</a>
        </div>

        <div class="d-flex justify-content-center mt-2">
            <button type="submit" class="btn"
                style="background:#A4823B;color:#F5F0E5;font-weight:700;border-radius:10px;padding:10px 20px;box-shadow:0 6px 18px rgba(164,130,59,0.08);">
                Save changes
            </button>
        </div>
    </form>
</section>
