<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->paginate(25);
        
        $activeCount = User::where('status', 'active')->count();
        return view('owner.users.index', compact('users', 'activeCount'));
    }

    public function create(): View
    {
        return view('owner.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['owner','waiter','chef','cashier','reviewer'])],
            'status' => ['required', Rule::in(['active','inactive'])],
        ]);

        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('owner.users')
            ->with('success', 'User created successfully!');
    }

    public function edit(User $user): View
    {
        return view('owner.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', Rule::in(['owner','waiter','chef','cashier','reviewer'])],
            'status' => ['required', Rule::in(['active','inactive'])],
        ]);

        $user->update($validated);

        return redirect()->route('owner.users')
            ->with('success', 'User updated successfully!');
    }
}