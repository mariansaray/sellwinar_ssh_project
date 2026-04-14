<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Webinar extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'name', 'slug', 'type', 'video_source', 'video_url',
        'video_duration_seconds', 'thumbnail_url', 'player_config', 'schedule_config',
        'registration_page_config', 'thankyou_page_config', 'chat_enabled',
        'cta_config', 'status',
    ];

    protected $casts = [
        'player_config' => 'array',
        'schedule_config' => 'array',
        'registration_page_config' => 'array',
        'thankyou_page_config' => 'array',
        'cta_config' => 'array',
        'chat_enabled' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(WebinarSchedule::class);
    }

    public function registrants(): HasMany
    {
        return $this->hasMany(Registrant::class);
    }

    public function chatMessagesFake(): HasMany
    {
        return $this->hasMany(ChatMessageFake::class);
    }

    public function chatMessagesReal(): HasMany
    {
        return $this->hasMany(ChatMessageReal::class);
    }

    public function chatConfig(): HasOne
    {
        return $this->hasOne(ChatConfig::class);
    }

    public function purchaseAlerts(): HasMany
    {
        return $this->hasMany(PurchaseAlert::class);
    }

    public function trackingPixels(): HasMany
    {
        return $this->hasMany(TrackingPixel::class);
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function smsTemplates(): HasMany
    {
        return $this->hasMany(SmsTemplate::class);
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class);
    }

    public function isEvergreen(): bool
    {
        return $this->type === 'evergreen';
    }

    public function isSmartVideo(): bool
    {
        return $this->type === 'smart_video';
    }
}
