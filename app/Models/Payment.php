<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHODS = [
        'cash' => 'Espèces',
        'card' => 'Carte',
        'mpesa' => 'M-Pesa',
        'orange_money' => 'Orange Money',
        'airtel_money' => 'Airtel Money',
    ];

    protected $fillable = [
        'order_id',
        'cash_session_id',
        'user_id',
        'method',
        'currency',
        'amount',
        'amount_usd',
        'reference',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_usd' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
