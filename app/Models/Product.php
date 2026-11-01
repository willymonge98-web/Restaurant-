<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'type',
        'description',
        'price',
        'cost_price',
        'track_stock',
        'stock_qty',
        'stock_alert',
        'unit',
        'is_active',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'track_stock' => 'boolean',
            'stock_qty' => 'decimal:3',
            'stock_alert' => 'decimal:3',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock_qty <= $this->stock_alert;
    }

    public function marginAmount(): float
    {
        return (float) $this->price - (float) $this->cost_price;
    }

    public function marginPercent(): float
    {
        if ((float) $this->price <= 0) {
            return 0;
        }

        return round(($this->marginAmount() / (float) $this->price) * 100, 2);
    }
}
