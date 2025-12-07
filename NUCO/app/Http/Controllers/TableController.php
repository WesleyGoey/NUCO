<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(Request $request): View
    {
        $tables = RestaurantTable::orderByRaw('CAST(table_number AS UNSIGNED)')->orderBy('table_number')->get();

        return view('waiter.tables', compact('tables'));
    }

    /**
     * Select a table (store in session) and redirect to menu-cart so waiter can add items to cart.
     */
    public function select(Request $request)
    {
        $request->validate(['table_id' => 'required|integer|exists:restaurant_tables,id']);

        $table = RestaurantTable::find($request->input('table_id'));
        if ($table) {
            session(['selected_table' => [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
            ]]);
        }

        // redirect to the menu-cart page (waiter flow)
        return redirect()->route('waiter.menu.cart');
    }
}