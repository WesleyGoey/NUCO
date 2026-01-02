@extends('layouts.mainlayout')

@section('title', 'Update Stock')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                <div class="card-header" style="background:#A4823B;padding:18px;">
                    <h4 class="m-0 fw-bold text-center" style="color:#F5F0E5;">Update Stock</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="mx-auto" style="width:80px;height:80px;border-radius:12px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-box-seam" style="font-size:2.5rem;color:#A4823B;"></i>
                        </div>
                        <div class="mt-2 fw-semibold" style="color:#4b3028;">{{ $ingredient->name }}</div>
                        <div class="small text-muted">Current Stock: 
                            <span class="fw-bold" style="color:#A4823B;">{{ number_format($ingredient->current_stock, 2) }} {{ $ingredient->unit }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('owner.inventory.stock.update', $ingredient) }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold" style="color:#4b3028;">Stock Update Type</label>
                                <div class="d-grid gap-2">
                                    <div class="form-check p-3" style="background:#E6F9EE; border-radius:10px; cursor:pointer;">
                                        <input class="form-check-input" type="radio" name="type" id="purchase" value="purchase" required checked>
                                        <label class="form-check-label w-100" for="purchase" style="cursor:pointer;">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-plus-circle fs-4 me-3" style="color:#2D7A3B;"></i>
                                                <div>
                                                    <div class="fw-semibold" style="color:#2D7A3B;">Stock In (Purchase)</div>
                                                    <div class="small text-muted">Add stock when goods arrive</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="form-check p-3" style="background:#FFECEC; border-radius:10px; cursor:pointer;">
                                        <input class="form-check-input" type="radio" name="type" id="waste" value="waste" required>
                                        <label class="form-check-label w-100" for="waste" style="cursor:pointer;">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-dash-circle fs-4 me-3" style="color:#C0392B;"></i>
                                                <div>
                                                    <div class="fw-semibold" style="color:#C0392B;">Stock Out (Waste)</div>
                                                    <div class="small text-muted">Remove stock due to damage/loss</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @error('type')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="amount" class="form-label fw-bold" style="color:#4b3028;">Amount ({{ $ingredient->unit }})</label>
                                <input id="amount" name="amount" type="number" step="0.01" class="form-control" required
                                       value="{{ old('amount') }}" min="0.01"
                                       placeholder="Enter amount to add or remove"
                                       style="border-radius:10px;padding:10px;">
                                @error('amount')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="note" class="form-label fw-bold" style="color:#4b3028;">Note (Optional)</label>
                                <textarea id="note" name="note" rows="3" class="form-control"
                                          placeholder="Add a note about this stock update..."
                                          style="border-radius:10px;padding:10px;">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="{{ route('owner.inventory.index') }}" class="btn btn-outline-secondary" 
                               style="border-radius:10px;padding:10px 20px;font-weight:600;">
                                Cancel
                            </a>
                            <button type="submit" class="btn" 
                                    style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 20px;font-weight:700;">
                                <i class="bi bi-check-lg me-2"></i> Update Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection