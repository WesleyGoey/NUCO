<?php

namespace App\Http\Controllers\waiter;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TableController extends Controller
{
    public function index(Request $request): View
    {
        $tables = RestaurantTable::orderByRaw('CAST(table_number AS UNSIGNED)')->orderBy('table_number')->get();

        // pass selected table id (if any) so view can mark selected table
        $selectedId = session('selected_table.id') ?? null;
        return view('waiter.tables', compact('tables', 'selectedId'));
    }

    /**
     * Select a table (store in session) and redirect to menu-cart so waiter can add items to cart.
     */
    public function select(Request $request): RedirectResponse
    {
        $request->validate(['table_id' => 'required|integer|exists:restaurant_tables,id']);

        $table = RestaurantTable::find($request->input('table_id'));
        if ($table) {
            // Update table status to occupied
            $table->update(['status' => 'occupied']);

            session(['selected_table' => [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
            ]]);
        }

        // redirect to the waiter cart page so waiter can add items
        return redirect()->route('waiter.cart');
    }

    /**
     * Cancel current order and release table
     */
    public function cancel(): RedirectResponse
    {
        $selectedTable = session('selected_table');
        
        if ($selectedTable && isset($selectedTable['id'])) {
            // Release table back to available
            $table = RestaurantTable::find($selectedTable['id']);
            if ($table) {
                $table->update(['status' => 'available']);
            }
        }

        // Clear cart and selected table from session
        session()->forget(['waiter_cart', 'selected_table']);

        return redirect()->route('waiter.tables');
    }
}