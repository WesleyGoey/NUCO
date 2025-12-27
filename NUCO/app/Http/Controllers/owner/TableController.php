<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\RestaurantTable;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = RestaurantTable::orderBy('id','asc')->paginate(25);
        
        // Count available tables
        $availableCount = RestaurantTable::where('status', 'available')->count();
        
        return view('owner.tables.index', compact('tables', 'availableCount'));
    }

    public function create(): View
    {
        return view('owner.tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:10', 'unique:restaurant_tables,table_number'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', 'in:available,occupied'],
        ]);

        RestaurantTable::create([
            'table_number' => $validated['table_number'],
            'capacity' => $validated['capacity'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('owner.tables.index')
            ->with('success', 'Table created successfully!');
    }

    public function edit(RestaurantTable $table): View
    {
        return view('owner.tables.edit', compact('table'));
    }

    public function update(Request $request, RestaurantTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:10', 'unique:restaurant_tables,table_number,' . $table->id],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', 'in:available,occupied'],
        ]);

        $table->update([
            'table_number' => $validated['table_number'],
            'capacity' => $validated['capacity'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('owner.tables.index')
            ->with('success', 'Table updated successfully!');
    }

    public function destroy(RestaurantTable $table): RedirectResponse
    {
        // Check if table has active orders
        $hasActiveOrders = $table->orders()
            ->whereIn('status', ['pending', 'processing', 'ready', 'sent'])
            ->exists();

        if ($hasActiveOrders) {
            return redirect()->route('owner.tables.index')
                ->with('error', 'Cannot delete table with active orders!');
        }

        $table->delete();

        return redirect()->route('owner.tables.index')
            ->with('success', 'Table deleted successfully!');
    }
}