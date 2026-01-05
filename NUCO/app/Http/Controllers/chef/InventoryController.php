<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Display inventory/stock for chef monitoring
     */
    public function inventory(Request $request): View
    {
        try {
            // Get search query if any
            $search = $request->query('search', '');

            // Query ingredients with search filter
            $query = Ingredient::query();

            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }

            // Order by stock status (low stock first), then by id
            $ingredients = $query->orderByRaw('CASE 
                    WHEN current_stock <= min_stock THEN 1 
                    WHEN current_stock <= min_stock * 1.5 THEN 2 
                    ELSE 3 
                END')
                ->orderBy('id', 'asc')
                ->get();

            $lowStock = $ingredients->filter(fn($ing) => $ing->current_stock <= $ing->min_stock)->count();
            $mediumStock = $ingredients->filter(fn($ing) => $ing->current_stock > $ing->min_stock && $ing->current_stock <= $ing->min_stock * 1.5)->count();
            $goodStock = $ingredients->filter(fn($ing) => $ing->current_stock > $ing->min_stock * 1.5)->count();

            return view('chef.inventory', compact('ingredients', 'search', 'lowStock', 'mediumStock', 'goodStock'));
        } catch (\Exception $e) {
            Log::error('Chef inventory error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('chef.inventory', [
                'ingredients' => collect(),
                'search' => $request->query('search', ''),
                'lowStock' => 0,
                'mediumStock' => 0,
                'goodStock' => 0,
                'error' => 'Failed to load inventory. Check logs.'
            ]);
        }
    }
}