<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class EmailTemplateGlobal extends Model
{
    use BelongsToTenant;

    protected $table = 'email_templates_global';

    protected $fillable = [
        'tenant_id', 'name', 'trigger_type', 'subject',
        'body_html', 'delay_minutes', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
