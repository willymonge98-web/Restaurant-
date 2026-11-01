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
        'image_path',
        'price',
        'cost_price',
        'track_stock',
        'stock_qty',
        'stock_alert',
        'unit',
        'is_active',
        'is_available',
    ];

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        // Support both public/images/... (committed demo assets) and storage/... uploads.
        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        return asset('storage/'.$this->image_path);
    }

    public function priceCdf(?float $rate = null): float
    {
        $rate ??= (float) RestaurantSetting::current()->exchange_rate;

        return round((float) $this->price * max(0.0001, $rate), 0);
    }

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
