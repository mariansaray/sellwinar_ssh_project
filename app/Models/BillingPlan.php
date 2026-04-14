<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'stripe_price_id', 'price', 'interval',
        'max_webinars', 'max_registrants', 'features', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];
}
