<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'currency',
        'max_users',
        'max_companies',
        'trial_days',
        'features',
        'allowed_plugins',
        'is_active',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'features'        => 'array',
            'allowed_plugins' => 'array',
            'is_active'       => 'boolean',
            'price_monthly'   => 'decimal:2',
            'price_yearly'    => 'decimal:2',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id')->whereIn('status', ['trial', 'active']);
    }

    public function getFormattedPriceAttribute(): string
    {
        return "{$this->currency} {$this->price_monthly}/mo";
    }
}
