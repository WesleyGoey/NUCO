<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
</head>

<body>
    @php
        $role = auth()->check() ? auth()->user()->role : 'guest';
    @endphp

    @if ($role === 'owner')
        @includeIf('layouts.navigation-owner')
    @elseif ($role === 'waiter')
        @includeIf('layouts.navigation-waiter')
    @elseif ($role === 'chef')
        @includeIf('layouts.navigation-chef')
    @elseif ($role === 'reviewer')
        @includeIf('layouts.navigation-reviewer')
    @else
        @includeIf('layouts.navigation')
    @endif

    <!-- Page Content -->
<main class="container my-4">
    @yield('content')
</main>

@includeIf('layouts.footer')

<!-- Bootstrap JS bundle (needed for collapse) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
