<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingHistory extends Model
{
    use BelongsToTenant;
    protected $table = 'billing_history';
    public $timestamps = false;

    protected $fillable = [
        'tenant_id', 'stripe_invoice_id', 'amount', 'currency',
        'status', 'period_start', 'period_end',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
