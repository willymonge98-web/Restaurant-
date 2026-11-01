<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const TYPE_DINE_IN = 'dine_in';
    public const TYPE_TAKEAWAY = 'takeaway';
    public const TYPE_DELIVERY = 'delivery';

    public const STATUS_OUVERTE = 'ouverte';
    public const STATUS_EN_PREPARATION = 'en_preparation';
    public const STATUS_PRETE = 'prete';
    public const STATUS_SERVIE = 'servie';
    public const STATUS_PAYEE = 'payee';
    public const STATUS_ANNULEE = 'annulee';

    protected $fillable = [
        'number',
        'type',
        'status',
        'dining_table_id',
        'user_id',
        'cashier_id',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'covers',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'paid_amount',
        'cost_total',
        'stock_deducted',
        'notes',
        'cancel_reason',
        'validated_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'cost_total' => 'decimal:2',
            'stock_deducted' => 'boolean',
            'validated_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function typeLabels(): array
    {
        return [
            self::TYPE_DINE_IN => 'Sur place',
            self::TYPE_TAKEAWAY => 'À emporter',
            self::TYPE_DELIVERY => 'Livraison',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_OUVERTE => 'Ouverte',
            self::STATUS_EN_PREPARATION => 'En préparation',
            self::STATUS_PRETE => 'Prête',
            self::STATUS_SERVIE => 'Servie',
            self::STATUS_PAYEE => 'Payée',
            self::STATUS_ANNULEE => 'Annulée',
        ];
    }

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function remainingBalance(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAYEE || $this->remainingBalance() <= 0.009;
    }

    public function recalculateTotals(?float $taxRate = null): void
    {
        $subtotal = (float) $this->items()->sum('line_total');
        $cost = (float) $this->items()->sum('line_cost');
        $discount = (float) $this->discount_amount;
        $taxable = max(0, $subtotal - $discount);
        $tax = $taxRate !== null ? round($taxable * ($taxRate / 100), 2) : (float) $this->tax_amount;

        $this->forceFill([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total' => round($taxable + $tax, 2),
            'cost_total' => $cost,
        ])->save();
    }
}
