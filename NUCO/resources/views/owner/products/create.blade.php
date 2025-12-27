@extends('layouts.mainlayout')

@section('title', 'Add Product')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                <div class="card-header" style="background:#A4823B;padding:18px;">
                    <h4 class="m-0 fw-bold text-center" style="color:#F5F0E5;">Add New Product</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="mx-auto" style="width:80px;height:80px;border-radius:12px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-card-list" style="font-size:2.5rem;color:#A4823B;"></i>
                        </div>
                        <div class="mt-2 fw-semibold" style="color:#4b3028;">Create New Menu Item</div>
                    </div>

                    <form method="POST" action="{{ route('owner.products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label fw-bold" style="color:#4b3028;">Product Name</label>
                                <input id="name" name="name" type="text" 
                                       value="{{ old('name') }}" 
                                       class="form-control" required
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('name')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="category_id" class="form-label fw-bold" style="color:#4b3028;">Category</label>
                                <select id="category_id" name="category_id" class="form-select" required
                                        style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                    <option value="">Select category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-bold" style="color:#4b3028;">Description</label>
                                <textarea id="description" name="description" rows="3"
                                       class="form-control" required
                                       style="border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="price" class="form-label fw-bold" style="color:#4b3028;">Price (Rp)</label>
                                <input id="price" name="price" type="number" 
                                       value="{{ old('price') }}" 
                                       class="form-control" required min="0"
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                @error('price')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="is_available" class="form-label fw-bold" style="color:#4b3028;">Availability</label>
                                <select id="is_available" name="is_available" class="form-select" required
                                        style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                    <option value="1" {{ old('is_available', '1') == '1' ? 'selected' : '' }}>Available</option>
                                    <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Unavailable</option>
                                </select>
                                @error('is_available')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="image" class="form-label fw-bold" style="color:#4b3028;">Product Image</label>
                                <input id="image" name="image" type="file" accept="image/*"
                                       class="form-control"
                                       style="height:48px;border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">
                                <div class="small text-muted mt-1">Max 2MB (JPG, PNG, GIF)</div>
                                @error('image')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="{{ route('owner.products.index') }}" class="btn btn-outline-secondary" 
                               style="padding:10px 24px;border-radius:10px;font-weight:600;">
                                Cancel
                            </a>
                            <button type="submit" class="btn" 
                                    style="background:#A4823B;color:#F5F0E5;padding:10px 24px;border-radius:10px;font-weight:700;">
                                <i class="bi bi-check-circle me-2"></i> Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection