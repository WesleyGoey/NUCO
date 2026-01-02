<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $selectedCategory = $request->query('category', '');

        $query = Product::with(['category', 'ingredients']);

        // Search filter
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Category filter
        if (!empty($selectedCategory)) {
            $query->where('category_id', $selectedCategory);
        }

        // Order alphabetically by name
        $products = $query->orderBy('name', 'asc')->paginate(30);
        
        $availableCount = Product::where('is_available', true)->count();
        $totalProductsCount = Product::count();
        
        // Get all categories for filter buttons
        $categories = Category::orderBy('name')->get();

        return view('owner.products.index', compact('products', 'availableCount', 'totalProductsCount', 'categories', 'search', 'selectedCategory'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('owner.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'integer', 'min:0'],
            'is_available' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'is_available' => $validated['is_available'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('owner.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('owner.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'integer', 'min:0'],
            'is_available' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('owner.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('owner.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    // Show recipe management page for a product
    public function showRecipe(Product $product): View
    {
        $product->load(['ingredients', 'category']);
        $allIngredients = Ingredient::orderBy('name')->get();
        
        return view('owner.products.recipe', compact('product', 'allIngredients'));
    }

    // Update product recipe (ingredients)
    public function updateRecipe(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'ingredients' => ['nullable', 'array'],
            'ingredients.*.id' => ['required', 'exists:ingredients,id'],
            'ingredients.*.amount' => ['required', 'numeric', 'min:0'],
        ]);

        // Sync ingredients with amounts
        if (!empty($validated['ingredients'])) {
            $sync = [];
            foreach ($validated['ingredients'] as $ing) {
                $sync[$ing['id']] = ['amount_needed' => $ing['amount']];
            }
            $product->ingredients()->sync($sync);
        } else {
            // Clear all ingredients if array is empty
            $product->ingredients()->sync([]);
        }

        return redirect()->route('owner.products.index')
            ->with('success', "Recipe for {$product->name} updated successfully!");
    }
}