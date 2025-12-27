<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Discount;
use App\Models\Period;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DiscountController extends Controller
{
    public function index(): View
    {
        $discounts = Discount::with('periods')->orderBy('id','asc')->paginate(25);
        
        // Count active discounts
        $today = now()->toDateString();
        $activeCount = Discount::whereHas('periods', function($q) use ($today) {
            $q->whereDate('start_date', '<=', $today)
              ->where(function($q2) use ($today) {
                  $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
              });
        })->count();
        
        return view('owner.discounts.index', compact('discounts', 'activeCount'));
    }

    public function create(): View
    {
        return view('owner.discounts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'integer', 'min:0'],
            'type' => ['required', 'in:percent,amount'],
            'min_order_amount' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'period_description' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function() use ($validated, $request) {
            // Create discount
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('discounts', 'public');
            }

            $discount = Discount::create([
                'name' => $validated['name'],
                'value' => $validated['value'],
                'type' => $validated['type'],
                'min_order_amount' => $validated['min_order_amount'],
                'image_path' => $imagePath,
            ]);

            // Create period
            Period::create([
                'discount_id' => $discount->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['period_description'],
            ]);
        });

        return redirect()->route('owner.discounts.index')
            ->with('success', 'Discount created successfully!');
    }

    public function edit(Discount $discount): View
    {
        $discount->load('periods');
        return view('owner.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'integer', 'min:0'],
            'type' => ['required', 'in:percent,amount'],
            'min_order_amount' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'period_description' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function() use ($validated, $request, $discount) {
            // Update discount
            if ($request->hasFile('image')) {
                if ($discount->image_path) {
                    Storage::disk('public')->delete($discount->image_path);
                }
                $validated['image_path'] = $request->file('image')->store('discounts', 'public');
            }

            $discount->update([
                'name' => $validated['name'],
                'value' => $validated['value'],
                'type' => $validated['type'],
                'min_order_amount' => $validated['min_order_amount'],
                'image_path' => $validated['image_path'] ?? $discount->image_path,
            ]);

            // Update or create period (assume 1 period per discount)
            $firstPeriod = $discount->periods()->first();
            
            if ($firstPeriod) {
                $firstPeriod->update([
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'description' => $validated['period_description'],
                ]);
            } else {
                Period::create([
                    'discount_id' => $discount->id,
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'description' => $validated['period_description'],
                ]);
            }
        });

        return redirect()->route('owner.discounts.index')
            ->with('success', 'Discount updated successfully!');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        DB::transaction(function() use ($discount) {
            // Delete image if exists
            if ($discount->image_path) {
                Storage::disk('public')->delete($discount->image_path);
            }

            // Periods will be deleted automatically via cascade
            $discount->delete();
        });

        return redirect()->route('owner.discounts.index')
            ->with('success', 'Discount deleted successfully!');
    }
}