<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    /**
     * Deduct stock for a validated sale via recipes (and tracked product stock).
     */
    public function deductForOrder(Order $order): void
    {
        if ($order->stock_deducted) {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->load(['items.product.recipe.items.ingredient']);

            foreach ($order->items as $item) {
                $product = $item->product;

                if ($product?->track_stock) {
                    $this->adjustProduct($product, -(float) $item->quantity, 'sale', $order, 'Vente commande '.$order->number);
                }

                $recipe = $product?->recipe;
                if (! $recipe || ! $recipe->is_active) {
                    continue;
                }

                foreach ($recipe->items as $recipeItem) {
                    $qty = (float) $recipeItem->quantity * (int) $item->quantity;
                    $this->adjustIngredient(
                        $recipeItem->ingredient,
                        -$qty,
                        'sale',
                        $order,
                        "Recette {$product->name} × {$item->quantity}"
                    );
                }
            }

            $order->forceFill(['stock_deducted' => true])->save();
        });
    }

    /**
     * Restore stock when a paid/validated sale is cancelled.
     */
    public function restoreForOrder(Order $order): void
    {
        if (! $order->stock_deducted) {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->load(['items.product.recipe.items.ingredient']);

            foreach ($order->items as $item) {
                $product = $item->product;

                if ($product?->track_stock) {
                    $this->adjustProduct($product, (float) $item->quantity, 'cancel_restore', $order, 'Annulation '.$order->number);
                }

                $recipe = $product?->recipe;
                if (! $recipe || ! $recipe->is_active) {
                    continue;
                }

                foreach ($recipe->items as $recipeItem) {
                    $qty = (float) $recipeItem->quantity * (int) $item->quantity;
                    $this->adjustIngredient(
                        $recipeItem->ingredient,
                        $qty,
                        'cancel_restore',
                        $order,
                        "Restauration recette {$product->name}"
                    );
                }
            }

            $order->forceFill(['stock_deducted' => false])->save();
        });
    }

    public function adjustIngredient(Ingredient $ingredient, float $delta, string $type, ?object $reference = null, ?string $notes = null): StockMovement
    {
        $before = (float) $ingredient->stock_qty;
        $after = $before + $delta;

        if ($after < -0.0001 && $type === 'sale') {
            throw new RuntimeException("Stock insuffisant pour l'ingrédient {$ingredient->name} (dispo: {$before}, besoin: ".abs($delta).').');
        }

        $ingredient->forceFill(['stock_qty' => max(0, $after)])->save();

        return StockMovement::query()->create([
            'movable_type' => Ingredient::class,
            'movable_id' => $ingredient->id,
            'type' => $type,
            'quantity' => $delta,
            'unit_cost' => $ingredient->unit_cost,
            'stock_before' => $before,
            'stock_after' => $ingredient->stock_qty,
            'reference_type' => $reference ? $reference::class : null,
            'reference_id' => $reference?->id ?? null,
            'user_id' => Auth::id(),
            'notes' => $notes,
        ]);
    }

    public function adjustProduct(Product $product, float $delta, string $type, ?object $reference = null, ?string $notes = null): StockMovement
    {
        $before = (float) $product->stock_qty;
        $after = $before + $delta;

        if ($after < -0.0001 && $type === 'sale') {
            throw new RuntimeException("Stock insuffisant pour le produit {$product->name}.");
        }

        $product->forceFill(['stock_qty' => max(0, $after)])->save();

        return StockMovement::query()->create([
            'movable_type' => Product::class,
            'movable_id' => $product->id,
            'type' => $type,
            'quantity' => $delta,
            'unit_cost' => $product->cost_price,
            'stock_before' => $before,
            'stock_after' => $product->stock_qty,
            'reference_type' => $reference ? $reference::class : null,
            'reference_id' => $reference?->id ?? null,
            'user_id' => Auth::id(),
            'notes' => $notes,
        ]);
    }
}
