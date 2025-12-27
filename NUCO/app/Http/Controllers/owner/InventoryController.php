<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Ingredient;

class InventoryController extends Controller
{
    public function index(): View
    {
        $ingredients = Ingredient::orderBy('name')->paginate(30);
        return view('owner.inventory.index', compact('ingredients'));
    }
}