<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use BelongsToTenant;
    protected $table = 'notification_logs';
    public $timestamps = false;

    protected $fillable = [
        'tenant_id', 'registrant_id', 'channel', 'template_id',
        'status', 'error_message', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }
}
