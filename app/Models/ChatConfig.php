<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConfig extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'webinar_id', 'tenant_id', 'viewer_count_min', 'viewer_count_max',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }
}
