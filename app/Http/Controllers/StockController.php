<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        return view('stock.index', [
            'ingredients' => Ingredient::orderBy('name')->paginate(30),
            'movements' => StockMovement::with('user')->latest()->limit(30)->get(),
        ]);
    }

    public function storeIngredient(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50'],
            'unit' => ['required', 'string', 'max:20'],
            'stock_qty' => ['nullable', 'numeric', 'min:0'],
            'stock_alert' => ['nullable', 'numeric', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        Ingredient::create([
            ...$data,
            'stock_qty' => $data['stock_qty'] ?? 0,
            'stock_alert' => $data['stock_alert'] ?? 0,
            'unit_cost' => $data['unit_cost'] ?? 0,
        ]);

        return back()->with('success', 'Ingrédient ajouté.');
    }

    public function adjust(Request $request, Ingredient $ingredient, StockService $stock): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out,adjust,loss'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'notes' => ['nullable', 'string', 'max:500'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $qty = (float) $data['quantity'];
        $delta = match ($data['type']) {
            'in' => $qty,
            'out', 'loss' => -$qty,
            'adjust' => $qty - (float) $ingredient->stock_qty,
        };

        if (isset($data['unit_cost'])) {
            $ingredient->update(['unit_cost' => $data['unit_cost']]);
        }

        try {
            $stock->adjustIngredient($ingredient->fresh(), $delta, $data['type'], null, $data['notes'] ?? null);
        } catch (\Throwable $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        }

        return back()->with('success', 'Mouvement de stock enregistré.');
    }
}
