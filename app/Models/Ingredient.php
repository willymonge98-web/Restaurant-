<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'unit',
        'stock_qty',
        'stock_alert',
        'unit_cost',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stock_qty' => 'decimal:3',
            'stock_alert' => 'decimal:3',
            'unit_cost' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->stock_alert;
    }
}
