<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use BelongsToTenant;
    public $timestamps = false;
    protected $table = 'analytics_events';

    protected $fillable = [
        'tenant_id', 'webinar_id', 'registrant_id', 'session_id',
        'event_type', 'event_data', 'ip_address', 'user_agent',
        'referrer_url', 'device_type',
    ];

    protected $casts = [
        'event_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }
}
