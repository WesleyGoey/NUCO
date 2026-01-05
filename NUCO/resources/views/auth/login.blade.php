<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - {{ config('app.name', 'NUCO') }}</title>

    <!-- CSRF token for forms / AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#F5F0E5; min-height:100vh; margin:0; font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial;">
    <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:28px;">
        <div style="width:100%; max-width:520px; border-radius:18px; padding:28px; background:#F5F0E5;">
            <div style="background:#ffffff; border-radius:12px; padding:36px; box-shadow:0 8px 24px rgba(0,0,0,0.06);">

                <div class="d-flex justify-content-center mb-3">
                    <div style="width:72px;height:72px;background:#F5F0E5;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name', 'NUCO') }}" style="width:48px;height:48px;object-fit:contain;">
                    </div>
                </div>

                <h2 class="text-center mb-2" style="color:#000000;font-weight:700;font-size:1.5rem;">Login</h2>

                <x-auth-session-status class="mb-3" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold" style="color:#000000;">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="form-control" style="border-radius:10px;border:1px solid #e9e6e2;padding:12px;"/>
                        @error('email') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold" style="color:#000000;">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               class="form-control" style="border-radius:10px;border:1px solid #e9e6e2;padding:12px;"/>
                        @error('password') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                    </div>

                    {{-- removed remember me & forgot password --}}
                    <div class="d-grid mb-3">
                        <button type="submit" style="background:#B8860B;color:#F5F0E5;border:none;padding:12px 14px;border-radius:10px;font-weight:700;">
                            LOG IN
                        </button>
                    </div>
                </form>

                {{-- removed register prompt --}}
            </div>

            {{-- bottom note removed --}}
        </div>
    </div>

    <!-- Bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
