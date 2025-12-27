@extends('layouts.mainlayout')

@section('title', 'Edit Discount')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                <div class="card-header" style="background:#A4823B;padding:18px;">
                    <h4 class="m-0 fw-bold text-center" style="color:#F5F0E5;">Edit Discount</h4>
                </div>
                <div class="card-body p-4">
                    @php
                        $firstPeriod = $discount->periods->first();
                    @endphp

                    <div class="text-center mb-4">
                        @if($discount->image_path)
                            <img src="{{ asset('storage/' . $discount->image_path) }}" 
                                 alt="{{ $discount->name }}"
                                 style="width:100px;height:100px;object-fit:cover;border-radius:12px;">
                        @else
                            <div class="mx-auto" style="width:80px;height:80px;border-radius:12px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-tag-fill" style="font-size:2.5rem;color:#A4823B;"></i>
                            </div>
                        @endif
                        <div class="mt-2 fw-semibold" style="color:#4b3028;">{{ $discount->name }}</div>
                        <div class="small text-muted">ID: {{ $discount->id }}</div>
                    </div>

                    <form method="POST" action="{{ route('owner.discounts.update', $discount) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label fw-bold" style="color:#4b3028;">Discount Name</label>
                                <input id="name" name="name" type="text" 
                                       value="{{ old('name', $discount->name) }}" 
                                       class="form-control" required
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('name')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="value" class="form-label fw-bold" style="color:#4b3028;">Value</label>
                                <input id="value" name="value" type="number" 
                                       value="{{ old('value', $discount->value) }}" 
                                       class="form-control" required min="0"
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('value')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="type" class="form-label fw-bold" style="color:#4b3028;">Type</label>
                                <select id="type" name="type" class="form-select" required
                                        style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                    <option value="percent" {{ old('type', $discount->type) == 'percent' ? 'selected' : '' }}>Percent (%)</option>
                                    <option value="amount" {{ old('type', $discount->type) == 'amount' ? 'selected' : '' }}>Amount (Rp)</option>
                                </select>
                                @error('type')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="min_order_amount" class="form-label fw-bold" style="color:#4b3028;">Min Order (Rp)</label>
                                <input id="min_order_amount" name="min_order_amount" type="number" 
                                       value="{{ old('min_order_amount', $discount->min_order_amount) }}" 
                                       class="form-control" min="0"
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('min_order_amount')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="image" class="form-label fw-bold" style="color:#4b3028;">Discount Image</label>
                                <input id="image" name="image" type="file" accept="image/*"
                                       class="form-control"
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                <div class="small text-muted mt-1">Leave empty to keep current image. Max 2MB (JPG, PNG, GIF)</div>
                                @error('image')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-3">
                                <h5 class="fw-bold mb-3" style="color:#4b3028;">Period Information</h5>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="start_date" class="form-label fw-bold" style="color:#4b3028;">Start Date</label>
                                <input id="start_date" name="start_date" type="date" 
                                       value="{{ old('start_date', $firstPeriod?->start_date) }}" 
                                       class="form-control" required
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('start_date')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="end_date" class="form-label fw-bold" style="color:#4b3028;">End Date</label>
                                <input id="end_date" name="end_date" type="date" 
                                       value="{{ old('end_date', $firstPeriod?->end_date) }}" 
                                       class="form-control"
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                <div class="small text-muted mt-1">Leave empty for ongoing</div>
                                @error('end_date')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="period_description" class="form-label fw-bold" style="color:#4b3028;">Period Description</label>
                                <textarea id="period_description" name="period_description" rows="3"
                                       class="form-control"
                                       style="border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">{{ old('period_description', $firstPeriod?->description) }}</textarea>
                                @error('period_description')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="{{ route('owner.discounts.index') }}" class="btn btn-outline-secondary" 
                               style="padding:10px 24px;border-radius:10px;font-weight:600;">
                                Cancel
                            </a>
                            <button type="submit" class="btn" 
                                    style="background:#A4823B;color:#F5F0E5;padding:10px 24px;border-radius:10px;font-weight:700;">
                                <i class="bi bi-check-circle me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection