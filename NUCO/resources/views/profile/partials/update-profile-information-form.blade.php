<section>
    <header class="mb-2">
        <h2 class="h5 fw-bold" style="color:#4b3028;">Profile Information</h2>
        <p class="small text-muted">Update your account details (username, email, phone).</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-2">
        @csrf
        @method('patch')

        @if (session('status') === 'profile-updated')
            <div class="alert mb-3 py-2" role="alert" style="background:#EAF3EA;border:1px solid rgba(164,130,59,0.06);color:#2D7A3B;">
                Profile updated!
            </div>
        @endif

        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-6">
                <label for="username" class="form-label fw-semibold" style="color:#4b3028;">Username</label>
                <input id="username" name="username" type="text" class="form-control shadow-sm"
                    value="{{ old('username', $user->username) }}" required autofocus autocomplete="username"
                    style="height:48px; border-radius:10px; border:1px solid rgba(164,130,59,0.12); padding:10px; background:#fff;">
                @error('username') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="phone" class="form-label fw-semibold" style="color:#4b3028;">Phone</label>
                <input id="phone" name="phone" type="text" class="form-control shadow-sm"
                    value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                    style="height:48px; border-radius:10px; border:1px solid rgba(164,130,59,0.12); padding:10px; background:#fff;">
                @error('phone') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="email" class="form-label fw-semibold" style="color:#4b3028;">Email</label>
                <input id="email" name="email" type="email" class="form-control shadow-sm"
                    value="{{ old('email', $user->email) }}" required autocomplete="username"
                    style="height:48px; border-radius:10px; border:1px solid rgba(164,130,59,0.12); padding:10px; background:#fff;">
                @error('email') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="text-center my-3">
            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#changePasswordModal"
               class="fw-semibold" style="color:#A4823B;text-decoration:underline;">Change password</a>
        </div>

        <div class="d-flex justify-content-center mt-2">
            <button type="submit" class="btn px-4 py-2"
                style="background:#A4823B;color:#F5F0E5;font-weight:700;border-radius:10px;box-shadow:0 6px 18px rgba(164,130,59,0.08);">
                Save changes
            </button>
        </div>
    </form>
</section>
