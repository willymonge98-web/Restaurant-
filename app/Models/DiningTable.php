<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiningTable extends Model
{
    public const STATUS_LIBRE = 'libre';
    public const STATUS_OCCUPEE = 'occupee';
    public const STATUS_RESERVEE = 'reservee';
    public const STATUS_NETTOYAGE = 'en_nettoyage';

    protected $fillable = [
        'name',
        'code',
        'capacity',
        'zone',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_LIBRE => 'Libre',
            self::STATUS_OCCUPEE => 'Occupée',
            self::STATUS_RESERVEE => 'Réservée',
            self::STATUS_NETTOYAGE => 'En nettoyage',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function openOrder(): HasOne
    {
        return $this->hasOne(Order::class)
            ->whereNotIn('status', ['payee', 'annulee'])
            ->latestOfMany();
    }
}
