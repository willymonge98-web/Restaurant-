<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role_id', 'phone', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function cashSessions(): HasMany
    {
        return $this->hasMany(CashSession::class);
    }

    public function openCashSession(): ?CashSession
    {
        return $this->cashSessions()->open()->latest('opened_at')->first();
    }

    public function hasRole(string|array $slugs): bool
    {
        if (! $this->role) {
            return false;
        }

        $slugs = (array) $slugs;

        return in_array($this->role->slug, $slugs, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function canAccess(string ...$roles): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->hasRole($roles);
    }
}
