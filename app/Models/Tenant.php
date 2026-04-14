<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'plan',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_status',
        'trial_ends_at',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'owner');
    }

    public function webinars(): HasMany
    {
        return $this->hasMany(Webinar::class);
    }

    public function registrants(): HasMany
    {
        return $this->hasMany(Registrant::class);
    }

    public function emailConfig(): HasOne
    {
        return $this->hasOne(EmailConfig::class);
    }

    public function smsConfig(): HasOne
    {
        return $this->hasOne(SmsConfig::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    public function billingHistory(): HasMany
    {
        return $this->hasMany(BillingHistory::class);
    }

    public function isTrialing(): bool
    {
        return $this->subscription_status === 'trialing'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function isActive(): bool
    {
        return in_array($this->subscription_status, ['active', 'trialing'])
            && ($this->subscription_status !== 'trialing' || $this->isTrialing());
    }
}
