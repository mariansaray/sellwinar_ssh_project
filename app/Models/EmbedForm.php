<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbedForm extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'webinar_id', 'form_config', 'domain_restrictions',
    ];

    protected $casts = [
        'form_config' => 'array',
        'domain_restrictions' => 'array',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }
}
