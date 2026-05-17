<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'points',
        'referral_code',
        'referred_by_user_id',
        'referral_credited_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'referral_credited_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ensureReferralCode(): string
    {
        if (! $this->referral_code) {
            do {
                $code = strtoupper(\Illuminate\Support\Str::random(8));
            } while (static::where('referral_code', $code)->exists());
            $this->forceFill(['referral_code' => $code])->save();
        }

        return $this->referral_code;
    }

    public function referrer()
    {
        return $this->belongsTo(self::class, 'referred_by_user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by_user_id');
    }

    public function getLifetimeSpendAttribute(): float
    {
        return (float) $this->orders()
            ->whereIn('status', ['paid', 'shipping', 'completed'])
            ->sum('total');
    }

    public function getTierAttribute(): array
    {
        return \App\Services\TierService::resolve($this->lifetime_spend);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->hasRole(['super_admin', 'staff']);
    }
}
