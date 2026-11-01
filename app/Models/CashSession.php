<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashSession extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'opening_float',
        'closing_counted',
        'theoretical_cash',
        'difference',
        'total_sales',
        'totals_by_method',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_float' => 'decimal:2',
            'closing_counted' => 'decimal:2',
            'theoretical_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'totals_by_method' => 'array',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
