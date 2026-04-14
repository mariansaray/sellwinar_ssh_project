<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessageFake;
use App\Models\ChatMessageReal;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get chat messages for viewer (fake messages synced to video time + admin replies)
     */
    public function index(Request $request)
    {
        $webinarId = $request->integer('webinar_id');
        $sessionId = $request->string('session_id');
        $currentSecond = $request->integer('current_second', 0);
        $since = $request->integer('since', 0);

        $messages = collect();

        // Fake messages up to current video second
        $fakeMessages = ChatMessageFake::withoutGlobalScopes()
            ->where('webinar_id', $webinarId)
            ->where('display_at_seconds', '<=', $currentSecond)
            ->orderBy('display_at_seconds')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => [
                'id' => 'fake_' . $m->id,
                'name' => $m->sender_name,
                'text' => $m->message_text,
                'type' => $m->message_type,
                'time' => $m->display_at_seconds,
            ]);

        $messages = $messages->merge($fakeMessages);

        // Real messages: admin replies to this viewer
        if ($sessionId) {
            $registrantId = $request->integer('registrant_id', 0);
            $realMessages = ChatMessageReal::withoutGlobalScopes()
                ->where('webinar_id', $webinarId)
                ->where(function ($q) use ($registrantId, $sessionId) {
                    $q->where(function ($q2) use ($registrantId) {
                        $q2->where('is_admin_reply', true)
                            ->where('reply_to_registrant_id', $registrantId);
                    })->orWhere(function ($q2) use ($sessionId) {
                        $q2->where('session_id', $sessionId)
                            ->where('is_admin_reply', false);
                    });
                })
                ->orderBy('created_at')
                ->get()
                ->map(fn ($m) => [
                    'id' => 'real_' . $m->id,
                    'name' => $m->sender_name,
                    'text' => $m->message_text,
                    'isAdmin' => $m->is_admin_reply,
                    'isOwn' => !$m->is_admin_reply,
                    'time' => $m->created_at->timestamp,
                ]);

            $messages = $messages->merge($realMessages);
        }

        return response()->json(['messages' => $messages->values()]);
    }

    /**
     * Send a message from viewer
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'webinar_id' => ['required', 'integer'],
            'registrant_id' => ['required', 'integer'],
            'session_id' => ['required', 'string', 'max:64'],
            'message' => ['required', 'string', 'max:500'],
            'sender_name' => ['required', 'string', 'max:100'],
        ]);

        $tenantId = \App\Models\Webinar::withoutGlobalScopes()
            ->where('id', $validated['webinar_id'])->value('tenant_id');

        ChatMessageReal::withoutGlobalScopes()->create([
            'webinar_id' => $validated['webinar_id'],
            'tenant_id' => $tenantId,
            'registrant_id' => $validated['registrant_id'],
            'session_id' => $validated['session_id'],
            'sender_name' => $validated['sender_name'],
            'message_text' => $validated['message'],
            'is_admin_reply' => false,
            'is_read_by_admin' => false,
        ]);

        return response()->json(['ok' => true]);
    }
}
