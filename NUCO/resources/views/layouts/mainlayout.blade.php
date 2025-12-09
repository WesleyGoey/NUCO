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
        $user = auth()->user();
        $isReviewer = $user && method_exists($user, 'isReviewer') && $user->isReviewer();
    @endphp

    @if ($user)
        @if (method_exists($user, 'isOwner') && $user->isOwner())
            @includeIf('layouts.navigation-owner')
        @elseif (method_exists($user, 'isWaiter') && $user->isWaiter())
            @includeIf('layouts.navigation-waiter')
        @elseif (method_exists($user, 'isChef') && $user->isChef())
            @includeIf('layouts.navigation-chef')
        @elseif (method_exists($user, 'isCashier') && $user->isCashier())
            @includeIf('layouts.navigation-cashier')
        @elseif ($isReviewer)
            @includeIf('layouts.navigation-reviewer')
        @else
            @includeIf('layouts.navigation')
        @endif
    @else
        @includeIf('layouts.navigation')
    @endif

    <!-- Page Content -->
    <div style="position:relative; left:50%; right:50%; margin-left:-50vw; margin-right:-50vw; width:100vw; background:#F5F0E5; min-height:100vh; padding:2.5rem 0;">
        <div class="container">
            <main class="my-4">
                @yield('content')
            </main>
        </div>
    </div>

   @unless($isReviewer)
       @includeIf('layouts.footer')
   @endunless

    <!-- Bootstrap JS bundle (needed for collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
