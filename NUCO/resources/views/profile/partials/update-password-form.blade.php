<section>
    <header>
        <h2 class="text-lg font-medium" style="color:#4b3028; font-weight:700;">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm" style="color:#6b655b;">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-3">
        @csrf
        @method('put')
        <div class="mb-3">
            <label for="current_password" class="form-label fw-bold" style="color:#4b3028;">Current Password</label>
            <input id="current_password" name="current_password" type="password" class="form-control shadow-sm"
                autocomplete="current-password"
                style="height:48px; border-radius:12px; border:1px solid rgba(164,130,59,0.12); background:#fff; padding:0.6rem 0.9rem; box-shadow: inset 0 1px 0 rgba(0,0,0,0.03);">
            @error('current_password', 'updatePassword')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label fw-bold" style="color:#4b3028;">New Password</label>
            <input id="password" name="password" type="password" class="form-control shadow-sm"
                autocomplete="new-password"
                style="height:48px; border-radius:12px; border:1px solid rgba(164,130,59,0.12); background:#fff; padding:0.6rem 0.9rem; box-shadow: inset 0 1px 0 rgba(0,0,0,0.03);">
            @error('password', 'updatePassword')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-bold" style="color:#4b3028;">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                class="form-control shadow-sm" autocomplete="new-password"
                style="height:48px; border-radius:12px; border:1px solid rgba(164,130,59,0.12); background:#fff; padding:0.6rem 0.9rem; box-shadow: inset 0 1px 0 rgba(0,0,0,0.03);">
            @error('password_confirmation', 'updatePassword')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-flex justify-content-center mt-4">
            <button type="submit" class="btn px-5 py-2"
                style="background:#A4823B;color:#F5F0E5;font-weight:700;border-radius:12px;box-shadow:0 8px 20px rgba(164,130,59,0.08);font-size:1rem;padding:10px 24px;">
                Change Password
            </button>
        </div>
        @if (session('status') === 'password-updated')
            <div class="text-center mt-3" style="background:#EAF3EA;border:1px solid rgba(164,130,59,0.06);color:#2D7A3B;padding:8px 12px;border-radius:8px;font-weight:600;">
                Password updated!
            </div>
        @endif
    </form>
</section>
