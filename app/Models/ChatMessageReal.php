<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessageReal extends Model
{
    use BelongsToTenant;
    protected $table = 'chat_messages_real';
    public $timestamps = false;

    protected $fillable = [
        'webinar_id', 'tenant_id', 'registrant_id', 'session_id',
        'sender_name', 'message_text', 'is_admin_reply',
        'reply_to_registrant_id', 'is_read_by_admin',
    ];

    protected $casts = [
        'is_admin_reply' => 'boolean',
        'is_read_by_admin' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }
}
