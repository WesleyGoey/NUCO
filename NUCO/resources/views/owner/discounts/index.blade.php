@extends('layouts.mainlayout')

@section('title', 'Discounts')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold" style="color:#4b3028;">Discounts Management</h3>
            <div class="small text-muted">Manage promos & periods</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="small text-muted">Active discounts</div>
                <div class="fw-bold" style="color:#A4823B; font-size:1.25rem;">{{ $activeCount }}</div>
            </div>
            <a href="{{ route('owner.discounts.create') }}" class="btn" 
               style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 18px;font-weight:700;">
                <i class="bi bi-plus-lg me-2"></i> Add Discount
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead style="background:#F5F0E5;">
                    <tr>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">#</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Image</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Name</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Value</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Type</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Min Order</th>
                        <th class="px-4 py-3" style="color:#4b3028; font-weight:700;">Period</th>
                        <th class="px-4 py-3 text-center" style="color:#4b3028; font-weight:700;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discounts as $discount)
                        @php
                            $firstPeriod = $discount->periods->first();
                            $today = now()->toDateString();
                            $isActive = false;
                            if ($firstPeriod) {
                                $isActive = $firstPeriod->start_date <= $today && 
                                           (!$firstPeriod->end_date || $firstPeriod->end_date >= $today);
                            }
                        @endphp
                        <tr style="border-bottom:1px solid #E9E6E2;">
                            <td class="px-4 py-3">{{ $discount->id }}</td>
                            <td class="px-4 py-3">
                                @if($discount->image_path)
                                    <img src="{{ asset('storage/' . $discount->image_path) }}" 
                                         alt="{{ $discount->name }}"
                                         style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                @else
                                    <div style="width:50px;height:50px;background:#F5F0E5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-tag" style="color:#A4823B;font-size:1.25rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="fw-semibold">{{ $discount->name }}</div>
                                @if($isActive)
                                    <span class="badge" style="background:#E6F9EE;color:#2D7A3B;font-weight:600;padding:4px 8px;border-radius:6px;font-size:0.75rem;">
                                        Active
                                    </span>
                                @else
                                    <span class="badge" style="background:#FFECEC;color:#C0392B;font-weight:600;padding:4px 8px;border-radius:6px;font-size:0.75rem;">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="fw-bold" style="color:#A4823B;">
                                    @if($discount->type === 'percent')
                                        {{ $discount->value }}%
                                    @else
                                        Rp {{ number_format($discount->value, 0, ',', '.') }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge text-capitalize" 
                                      style="background:{{ $discount->type === 'percent' ? '#E6F0FF' : '#FFF4E6' }};
                                             color:{{ $discount->type === 'percent' ? '#2E5C8A' : '#D68910' }};
                                             font-weight:600;padding:6px 12px;border-radius:8px;">
                                    {{ $discount->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($discount->min_order_amount)
                                    <span class="small text-muted">Rp {{ number_format($discount->min_order_amount, 0, ',', '.') }}</span>
                                @else
                                    <span class="small text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($firstPeriod)
                                    <div class="small">
                                        <div>{{ \Carbon\Carbon::parse($firstPeriod->start_date)->format('d M Y') }}</div>
                                        <div class="text-muted">
                                            to {{ $firstPeriod->end_date ? \Carbon\Carbon::parse($firstPeriod->end_date)->format('d M Y') : 'Ongoing' }}
                                        </div>
                                    </div>
                                @else
                                    <span class="small text-muted">No period</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('owner.discounts.edit', $discount) }}" 
                                       class="btn btn-sm" 
                                       style="background:#A4823B;color:#F5F0E5;border-radius:8px;padding:6px 14px;font-weight:600;">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('owner.discounts.destroy', $discount) }}" method="POST" 
                                          onsubmit="return confirm('Delete this discount and its periods?');">
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
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size:2rem;color:#E9E6E2;"></i>
                                <div class="mt-2">No discounts found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($discounts->hasPages())
            <div class="p-3 border-top" style="background:#FAFAFA;">
                {{ $discounts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection