<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingPixel extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'webinar_id', 'pixel_type', 'pixel_id',
        'page_placement', 'custom_events', 'is_active',
    ];

    protected $casts = [
        'page_placement' => 'array',
        'custom_events' => 'array',
        'is_active' => 'boolean',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }
}
