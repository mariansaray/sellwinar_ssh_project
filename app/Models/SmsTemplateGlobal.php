<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class SmsTemplateGlobal extends Model
{
    use BelongsToTenant;

    protected $table = 'sms_templates_global';

    protected $fillable = [
        'tenant_id', 'name', 'trigger_type', 'message_text',
        'delay_minutes', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
