<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class SmsConfig extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'twilio_sid', 'twilio_token_encrypted',
        'twilio_phone', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = ['twilio_token_encrypted'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function setTwilioTokenAttribute(string $value): void
    {
        $this->attributes['twilio_token_encrypted'] = Crypt::encryptString($value);
    }

    public function getTwilioTokenAttribute(): ?string
    {
        return $this->twilio_token_encrypted
            ? Crypt::decryptString($this->twilio_token_encrypted)
            : null;
    }
}
