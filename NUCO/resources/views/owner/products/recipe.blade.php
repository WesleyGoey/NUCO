@extends('layouts.mainlayout')

@section('title', 'Manage Recipe')

@section('content')
<div class="container-xl py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
                <div class="card-header" style="background:#A4823B;padding:18px;">
                    <h4 class="m-0 fw-bold text-center" style="color:#F5F0E5;">Manage Recipe: {{ $product->name }}</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" 
                                 style="width:100px;height:100px;object-fit:cover;border-radius:12px;">
                        @else
                            <div class="mx-auto" style="width:80px;height:80px;border-radius:12px;background:#F5F0E5;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-card-list" style="font-size:2.5rem;color:#A4823B;"></i>
                            </div>
                        @endif
                        <div class="mt-2 fw-semibold" style="color:#4b3028;">{{ $product->name }}</div>
                        <div class="small text-muted">{{ $product->category->name ?? '-' }} • Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    </div>

                    <form method="POST" action="{{ route('owner.products.recipe.update', $product) }}" id="recipeForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color:#4b3028;">Ingredients</label>
                            <div id="ingredientsContainer">
                                @forelse($product->ingredients as $ing)
                                    <div class="ingredient-row row g-3 mb-3 align-items-end">
                                        <div class="col-12 col-md-7">
                                            <select name="ingredients[{{ $loop->index }}][id]" class="form-select" required style="border-radius:10px;padding:10px;">
                                                <option value="">Select ingredient</option>
                                                @foreach($allIngredients as $opt)
                                                    <option value="{{ $opt->id }}" {{ $opt->id == $ing->id ? 'selected' : '' }}>
                                                        {{ $opt->name }} ({{ $opt->unit }}) — Stock: {{ number_format($opt->current_stock, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <input type="number" step="0.01" name="ingredients[{{ $loop->index }}][amount]" 
                                                   value="{{ $ing->pivot->amount_needed }}" class="form-control" placeholder="Amount" required 
                                                   style="border-radius:10px;padding:10px;">
                                        </div>
                                        <div class="col-12 col-md-1">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-ingredient" style="border-radius:8px;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    {{-- No ingredients yet --}}
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="addIngredient" style="border-radius:8px;">
                                <i class="bi bi-plus-lg me-1"></i> Add Ingredient
                            </button>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="{{ route('owner.products.index') }}" class="btn btn-outline-secondary" 
                               style="border-radius:10px;padding:10px 20px;font-weight:600;">
                                Cancel
                            </a>
                            <button type="submit" class="btn" 
                                    style="background:#A4823B;color:#F5F0E5;border-radius:10px;padding:10px 20px;font-weight:700;">
                                <i class="bi bi-check-lg me-2"></i> Update Recipe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('ingredientsContainer');
    const addBtn = document.getElementById('addIngredient');
    let counter = {{ $product->ingredients->count() }};

    // Build ingredients options HTML string
    const ingredientsOptions = `
        <option value="">Select ingredient</option>
        @foreach($allIngredients as $opt)
            <option value="{{ $opt->id }}">{{ $opt->name }} ({{ $opt->unit }}) — Stock: {{ number_format($opt->current_stock, 2) }}</option>
        @endforeach
    `;

    addBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'ingredient-row row g-3 mb-3 align-items-end';
        newRow.innerHTML = `
            <div class="col-12 col-md-7">
                <select name="ingredients[` + counter + `][id]" class="form-select" required style="border-radius:10px;padding:10px;">
                    ` + ingredientsOptions + `
                </select>
            </div>
            <div class="col-12 col-md-4">
                <input type="number" step="0.01" name="ingredients[` + counter + `][amount]" 
                       class="form-control" placeholder="Amount" required 
                       style="border-radius:10px;padding:10px;">
            </div>
            <div class="col-12 col-md-1">
                <button type="button" class="btn btn-outline-danger w-100 remove-ingredient" style="border-radius:8px;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(newRow);
        counter++;
    });

    // Event delegation for remove buttons
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-ingredient')) {
            e.target.closest('.ingredient-row').remove();
        }
    });
});
</script>
@endsection