<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registrant extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'webinar_id', 'email', 'first_name', 'last_name',
        'phone', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term',
        'utm_content', 'custom_fields', 'scheduled_at', 'access_token',
        'status', 'registration_ip', 'user_agent',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessageReal::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}
