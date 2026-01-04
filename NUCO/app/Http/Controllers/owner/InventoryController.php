<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Ingredient;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display ingredients (READ-ONLY) for owner to monitor stock
     */
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        
        $query = Ingredient::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        // ✅ FIXED: Order by stock status (Low → Medium → Good), then by ID
        $ingredients = $query->orderByRaw('CASE 
                WHEN current_stock <= min_stock THEN 1 
                WHEN current_stock <= min_stock * 1.5 THEN 2 
                ELSE 3 
            END')
            ->orderBy('id', 'asc') // ✅ CHANGED: from name to id
            ->paginate(20);
        
        $lowStock = Ingredient::whereRaw('current_stock <= min_stock')->count();
        $mediumStock = Ingredient::whereRaw('current_stock > min_stock AND current_stock <= min_stock * 1.5')->count();
        $goodStock = Ingredient::whereRaw('current_stock > min_stock * 1.5')->count();

        return view('owner.inventory.index', compact('ingredients', 'search', 'lowStock', 'mediumStock', 'goodStock'));
    }

    // Show create form
    public function create(): View
    {
        return view('owner.inventory.create');
    }

    // Store new ingredient
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'numeric', 'min:0'],
        ]);

        Ingredient::create($validated);

        return redirect()->route('owner.inventory.index')
            ->with('success', 'Ingredient created successfully!');
    }

    // Show edit form
    public function edit(Ingredient $ingredient): View
    {
        return view('owner.inventory.edit', compact('ingredient'));
    }

    // Update ingredient (name, unit, min_stock only — NOT current_stock)
    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'min_stock' => ['required', 'numeric', 'min:0'],
        ]);

        $ingredient->update($validated);

        return redirect()->route('owner.inventory.index')
            ->with('success', 'Ingredient updated successfully!');
    }

    // Delete ingredient
    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        // Check if ingredient is used in any product
        if ($ingredient->products()->count() > 0) {
            return back()->with('error', 'Cannot delete ingredient that is used in products!');
        }

        $ingredient->delete();

        return redirect()->route('owner.inventory.index')
            ->with('success', 'Ingredient deleted successfully!');
    }

    // Show stock update form (manual stock adjustment)
    public function showStockForm(Ingredient $ingredient): View
    {
        return view('owner.inventory.stock', compact('ingredient'));
    }

    // Update stock (manual stock in/out with log)
    public function updateStock(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:purchase,waste'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $changeAmount = $validated['type'] === 'purchase' 
            ? (float)$validated['amount'] 
            : -(float)$validated['amount'];

        // Update current stock
        $newStock = $ingredient->current_stock + $changeAmount;
        
        if ($newStock < 0) {
            return back()->with('error', 'Stock cannot be negative!');
        }

        $ingredient->update(['current_stock' => $newStock]);

        // Log inventory change
        InventoryLog::create([
            'ingredient_id' => $ingredient->id,
            'user_id' => Auth::id(),
            'change_amount' => $changeAmount,
            'type' => $validated['type'],
        ]);

        return redirect()->route('owner.inventory.index')
            ->with('success', "Stock updated successfully! New stock: {$newStock} {$ingredient->unit}");
    }
}