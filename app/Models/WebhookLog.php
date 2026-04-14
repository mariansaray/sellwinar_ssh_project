<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    use BelongsToTenant;
    protected $table = 'webhook_logs';
    public $timestamps = false;

    protected $fillable = [
        'webhook_id', 'tenant_id', 'event_type', 'payload',
        'response_code', 'response_body', 'attempt', 'status',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
