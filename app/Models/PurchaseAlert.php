<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseAlert extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'webinar_id', 'tenant_id', 'buyer_name', 'product_name',
        'display_at_seconds', 'sort_order',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }
}
