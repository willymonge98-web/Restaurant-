<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function index(): View
    {
        return view('recipes.index', [
            'recipes' => Recipe::with(['product', 'items.ingredient'])->orderBy('id')->get(),
            'products' => Product::whereDoesntHave('recipe')->where('type', 'food')->orderBy('name')->get(),
            'ingredients' => Ingredient::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id', 'unique:recipes,product_id'],
            'name' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ]);

        DB::transaction(function () use ($data) {
            $recipe = Recipe::create([
                'product_id' => $data['product_id'],
                'name' => $data['name'] ?? null,
                'is_active' => true,
            ]);

            foreach ($data['items'] as $item) {
                $recipe->items()->create([
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $cost = $recipe->computeCost();
            $recipe->product->update(['cost_price' => $cost]);
        });

        return back()->with('success', 'Recette créée — coût produit recalculé.');
    }

    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ]);

        DB::transaction(function () use ($recipe, $data, $request) {
            $recipe->update([
                'name' => $data['name'] ?? $recipe->name,
                'is_active' => $request->boolean('is_active', true),
            ]);
            $recipe->items()->delete();
            foreach ($data['items'] as $item) {
                $recipe->items()->create([
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            $recipe->product->update(['cost_price' => $recipe->fresh()->computeCost()]);
        });

        return back()->with('success', 'Recette mise à jour.');
    }
}
