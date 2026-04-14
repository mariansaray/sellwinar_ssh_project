<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessageFake extends Model
{
    use BelongsToTenant;
    protected $table = 'chat_messages_fake';
    public $timestamps = false;

    protected $fillable = [
        'webinar_id', 'tenant_id', 'sender_name', 'message_text',
        'display_at_seconds', 'message_type', 'sort_order',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }
}
