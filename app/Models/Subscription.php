<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\Company;
use Webkul\Security\Models\User;

class Subscription extends Model
{
    protected $fillable = [
        'company_id',
        'plan_id',
        'status',
        'billing_cycle',
        'amount',
        'currency',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'payment_method',
        'external_id',
        'meta',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'cancelled_at'  => 'datetime',
            'meta'          => 'array',
            'amount'        => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['trial', 'active']);
    }

    public function isOnTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at?->isFuture();
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function daysRemaining(): int
    {
        if ($this->isOnTrial()) {
            return (int) now()->diffInDays($this->trial_ends_at, false);
        }

        if ($this->ends_at) {
            return (int) now()->diffInDays($this->ends_at, false);
        }

        return 0;
    }
}
