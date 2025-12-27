<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChefController extends Controller
{
    /**
     * Display inventory/stock for chef monitoring
     */
    public function inventory(Request $request): View
    {
        // Get search query if any
        $search = $request->query('search', '');

        // Query ingredients with search filter
        $query = Ingredient::query();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Order by stock status (low stock first), then by name
        $ingredients = $query->orderByRaw('CASE 
                WHEN current_stock <= min_stock THEN 1 
                WHEN current_stock <= min_stock * 1.5 THEN 2 
                ELSE 3 
            END')
            ->orderBy('name', 'asc')
            ->get();

        // Categorize ingredients by stock level
        $lowStock = $ingredients->filter(function ($ing) {
            return $ing->current_stock <= $ing->min_stock;
        })->count();

        $mediumStock = $ingredients->filter(function ($ing) {
            return $ing->current_stock > $ing->min_stock && $ing->current_stock <= $ing->min_stock * 1.5;
        })->count();

        $goodStock = $ingredients->filter(function ($ing) {
            return $ing->current_stock > $ing->min_stock * 1.5;
        })->count();

        return view('chef.inventory', compact('ingredients', 'search', 'lowStock', 'mediumStock', 'goodStock'));
    }
}