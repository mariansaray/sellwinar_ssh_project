<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class EmailConfig extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'smtp_host', 'smtp_port', 'smtp_user',
        'smtp_pass_encrypted', 'from_name', 'from_email', 'reply_to', 'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    protected $hidden = ['smtp_pass_encrypted'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function setSmtpPasswordAttribute(string $value): void
    {
        $this->attributes['smtp_pass_encrypted'] = Crypt::encryptString($value);
    }

    public function getSmtpPasswordAttribute(): ?string
    {
        return $this->smtp_pass_encrypted
            ? Crypt::decryptString($this->smtp_pass_encrypted)
            : null;
    }
}
