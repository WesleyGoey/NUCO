@extends('layouts.mainlayout')

@section('title', 'Tables')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold" style="color:#4b3028;">Tables Management</h3>
            <div class="small text-muted">Manage dining layout</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="small text-muted">Available tables</div>
                <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">{{ $availableCount }}</div>
            </div>
            <a href="{{ route('owner.tables.create') }}" class="btn" 
               style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 18px;font-weight:700;">
                <i class="bi bi-plus-lg me-2"></i> Add Table
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead style="background:#F5F0E5;">
                    <tr>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">#</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Table Number</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Capacity</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Status</th>
                        <th class="px-4 py-3 text-center" style="color:#4b3028; font-weight:700;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tables as $table)
                        <tr style="border-bottom:1px solid #E9E6E2;">
                            <td class="px-4 py-3">{{ $table->id }}</td>
                            <td class="px-4 py-3">
                                <div class="fw-semibold">Table {{ $table->table_number }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-secondary">{{ $table->capacity }} seats</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($table->status === 'available')
                                    <span class="badge" style="background:#E6F9EE;color:#2D7A3B;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        Available
                                    </span>
                                @else
                                    <span class="badge" style="background:#FFF4E6;color:#D68910;font-weight:600;padding:6px 12px;border-radius:8px;">
                                        Occupied
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('owner.tables.edit', $table) }}" 
                                       class="btn btn-sm" 
                                       style="background:#A4823B;color:#F5F0E5;border-radius:8px;padding:6px 14px;font-weight:600;">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('owner.tables.destroy', $table) }}" method="POST" 
                                          onsubmit="return confirm('Delete Table {{ $table->table_number }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" style="border-radius:8px;padding:6px 14px;font-weight:600;">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size:2rem;color:#E9E6E2;"></i>
                                <div class="mt-2">No tables found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tables->hasPages())
            <div class="p-3 border-top" style="background:#FAFAFA;">
                {{ $tables->links() }}
            </div>
        @endif
    </div>
</div>
@endsection