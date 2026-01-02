@extends('layouts.mainlayout')

@section('title', 'Edit Ingredient')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                <div class="card-header" style="background:#A4823B;padding:18px;">
                    <h4 class="m-0 fw-bold text-center" style="color:#F5F0E5;">Edit Ingredient</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="mx-auto" style="width:80px;height:80px;border-radius:12px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-box-seam" style="font-size:2.5rem;color:#A4823B;"></i>
                        </div>
                        <div class="mt-2 fw-semibold" style="color:#4b3028;">{{ $ingredient->name }}</div>
                        <div class="small text-muted">Current Stock: {{ number_format($ingredient->current_stock, 2) }} {{ $ingredient->unit }}</div>
                    </div>

                    <form method="POST" action="{{ route('owner.inventory.update', $ingredient) }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label fw-bold" style="color:#4b3028;">Ingredient Name</label>
                                <input id="name" name="name" type="text" class="form-control" required
                                       value="{{ old('name', $ingredient->name) }}"
                                       style="border-radius:10px;padding:10px;">
                                @error('name')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="unit" class="form-label fw-bold" style="color:#4b3028;">Unit</label>
                                <input id="unit" name="unit" type="text" class="form-control" required
                                       value="{{ old('unit', $ingredient->unit) }}"
                                       style="border-radius:10px;padding:10px;">
                                @error('unit')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="min_stock" class="form-label fw-bold" style="color:#4b3028;">Min Stock</label>
                                <input id="min_stock" name="min_stock" type="number" step="0.01" class="form-control" required
                                       value="{{ old('min_stock', $ingredient->min_stock) }}"
                                       style="border-radius:10px;padding:10px;">
                                @error('min_stock')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mt-3" style="border-radius:10px;">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> To update current stock, use the "Stock" button.
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="{{ route('owner.inventory.index') }}" class="btn btn-outline-secondary" 
                               style="border-radius:10px;padding:10px 20px;font-weight:600;">
                                Cancel
                            </a>
                            <button type="submit" class="btn" 
                                    style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 20px;font-weight:700;">
                                <i class="bi bi-check-lg me-2"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection