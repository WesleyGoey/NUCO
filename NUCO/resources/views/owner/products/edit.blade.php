@extends('layouts.mainlayout')

@section('title', 'Edit Product')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                <div class="card-header" style="background:#A4823B;padding:18px;">
                    <h4 class="m-0 fw-bold text-center" style="color:#F5F0E5;">Edit Product</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" 
                                 alt="{{ $product->name }}"
                                 style="width:100px;height:100px;object-fit:cover;border-radius:12px;">
                        @else
                            <div class="mx-auto" style="width:80px;height:80px;border-radius:12px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-card-list" style="font-size:2.5rem;color:#A4823B;"></i>
                            </div>
                        @endif
                        <div class="mt-2 fw-semibold" style="color:#4b3028;">{{ $product->name }}</div>
                        <div class="small text-muted">ID: {{ $product->id }}</div>
                    </div>

                    <form method="POST" action="{{ route('owner.products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label fw-bold" style="color:#4b3028;">Product Name</label>
                                <input id="name" name="name" type="text" 
                                       value="{{ old('name', $product->name) }}" 
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
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
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
                                       style="border-radius:10px;border:1px solid rgba(164,130,59,0.12);padding:0.6rem 0.9rem;">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="price" class="form-label fw-bold" style="color:#4b3028;">Price (Rp)</label>
                                <input id="price" name="price" type="number" 
                                       value="{{ old('price', $product->price) }}" 
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
                                    <option value="1" {{ old('is_available', $product->is_available) == '1' ? 'selected' : '' }}>Available</option>
                                    <option value="0" {{ old('is_available', $product->is_available) == '0' ? 'selected' : '' }}>Unavailable</option>
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
                                <div class="small text-muted mt-1">Leave empty to keep current image. Max 2MB (JPG, PNG, GIF)</div>
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