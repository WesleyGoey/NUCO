@extends('layouts.mainlayout')

@section('title', 'Users')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold" style="color:#4b3028;">Users Management</h3>
            <div class="small text-muted">Manage staff accounts</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="small text-muted">Active users</div>
                <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">{{ $activeCount }}</div>
            </div>
            <a href="{{ route('owner.users.create') }}" class="btn" 
               style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 18px;font-weight:700;">
                <i class="bi bi-plus-lg me-2"></i> Add User
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead style="background:#F5F0E5;">
                    <tr>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">#</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Username</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Email</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Phone</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Role</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Status</th>
                        <th class="px-4 py-3 text-center" style="color:#4b3028; font-weight:700;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="border-bottom:1px solid #E9E6E2;">
                            <td class="px-4 py-3">{{ $user->id }}</td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:36px;height:36px;border-radius:8px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-person-fill" style="color:#A4823B;font-size:1rem;"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $user->username }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ $user->phone }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $roleColors = [
                                        'owner' => ['bg' => '#FFF4E6', 'text' => '#D68910'],
                                        'waiter' => ['bg' => '#E6F9EE', 'text' => '#2D7A3B'],
                                        'chef' => ['bg' => '#FFE6E6', 'text' => '#C0392B'],
                                        'cashier' => ['bg' => '#E6F0FF', 'text' => '#2E5C8A'],
                                        'reviewer' => ['bg' => '#F3E6FF', 'text' => '#7B3FA8'],
                                    ];
                                    $role = $user->role ?? 'owner';
                                    $colors = $roleColors[$role] ?? ['bg' => '#F5F0E5', 'text' => '#A4823B'];
                                @endphp
                                <span class="badge text-capitalize" 
                                      style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};font-weight:600;padding:6px 12px;border-radius:8px;">
                                    {{ $user->role ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if(($user->status ?? '') === 'active')
                                    <span class="badge" style="background:#E6F9EE;color:#2D7A3B;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        Active
                                    </span>
                                @else
                                    <span class="badge" style="background:#FFECEC;color:#C0392B;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('owner.users.edit', $user) }}" 
                                   class="btn btn-sm" 
                                   style="background:#A4823B;color:#F5F0E5;border-radius:8px;padding:6px 14px;font-weight:600;">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size:2rem;color:#E9E6E2;"></i>
                                <div class="mt-2">No users found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-3 border-top" style="background:#FAFAFA;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection