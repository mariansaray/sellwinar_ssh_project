<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'webinar_id', 'trigger_type', 'subject',
        'body_html', 'is_active', 'delay_minutes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }
}
