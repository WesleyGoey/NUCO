<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // redirect by role
        $user = $request->user();
        
        // Waiter -> tables
        if ($user && method_exists($user, 'isWaiter') && $user->isWaiter()) {
            return redirect()->intended(route('waiter.tables', absolute: false));
        }

        // Owner -> dashboard
        if ($user && method_exists($user, 'isOwner') && $user->isOwner()) {
            return redirect()->intended(route('owner.dashboard', absolute: false));
        }

        // Cashier -> checkout
        if ($user && method_exists($user, 'isCashier') && $user->isCashier()) {
            return redirect()->intended(route('cashier.checkout', absolute: false));
        }

        // Reviewer -> reviews
        if ($user && method_exists($user, 'isReviewer') && $user->isReviewer()) {
            return redirect()->intended(route('reviewer.reviews', absolute: false));
        }

        // Chef -> orders (with pending filter)
        if ($user && method_exists($user, 'isChef') && $user->isChef()) {
            return redirect()->intended(route('orders', absolute: false));
        }

        // Default -> dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
