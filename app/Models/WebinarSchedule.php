<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarSchedule extends Model
{
    protected $fillable = [
        'webinar_id', 'tenant_id', 'schedule_type', 'fixed_times',
        'jit_delay_minutes', 'interval_hours', 'timezone', 'hide_night_times',
    ];

    protected $casts = [
        'fixed_times' => 'array',
        'hide_night_times' => 'boolean',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }
}
