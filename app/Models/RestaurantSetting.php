<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantSetting extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
        'address',
        'phone',
        'email',
        'currency_primary',
        'currency_secondary',
        'exchange_rate',
        'ticket_footer',
        'tax_label',
        'tax_rate',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:4',
            'tax_rate' => 'decimal:2',
        ];
    }

    public static function current(): self
    {
        $existing = static::query()->first();
        if ($existing) {
            return $existing;
        }

        return static::query()->create([
            'name' => 'LÉNA RESTAURANT',
            'currency_primary' => 'USD',
            'currency_secondary' => 'CDF',
            'exchange_rate' => 2800,
            'ticket_footer' => 'Merci de votre visite !',
        ]);
    }
}
