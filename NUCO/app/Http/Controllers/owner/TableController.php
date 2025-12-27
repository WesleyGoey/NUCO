<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\RestaurantTable;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = RestaurantTable::orderByRaw('CAST(table_number AS UNSIGNED)')->get();
        return view('owner.tables.index', compact('tables'));
    }
}