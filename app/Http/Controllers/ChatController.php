<?php

namespace App\Http\Controllers;

use App\Models\ChatConfig;
use App\Models\ChatMessageFake;
use App\Models\ChatMessageReal;
use App\Models\Webinar;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Fake messages CRUD
     */
    public function fakeMessages(Webinar $webinar)
    {
        $messages = $webinar->chatMessagesFake()->orderBy('display_at_seconds')->get();
        $chatConfig = $webinar->chatConfig ?? new ChatConfig(['viewer_count_min' => 45, 'viewer_count_max' => 120]);

        return view('webinars.chat.fake-messages', compact('webinar', 'messages', 'chatConfig'));
    }

    public function storeFakeMessage(Request $request, Webinar $webinar)
    {
        $validated = $request->validate([
            'sender_name' => ['required', 'string', 'max:100'],
            'message_text' => ['required', 'string', 'max:500'],
            'display_at_seconds' => ['required', 'integer', 'min:0'],
            'message_type' => ['in:message,question,reaction,system'],
        ]);

        $webinar->chatMessagesFake()->create(array_merge($validated, [
            'tenant_id' => $webinar->tenant_id,
            'sort_order' => ChatMessageFake::withoutGlobalScopes()->where('webinar_id', $webinar->id)->max('sort_order') + 1,
        ]));

        return back()->with('success', 'Správa bola pridaná.');
    }

    public function destroyFakeMessage(Webinar $webinar, ChatMessageFake $message)
    {
        $message->delete();
        return back()->with('success', 'Správa bola zmazaná.');
    }

    public function importFakeMessages(Request $request, Webinar $webinar)
    {
        $request->validate(['csv_data' => ['required', 'string']]);

        $lines = array_filter(explode("\n", $request->csv_data));
        $count = 0;

        foreach ($lines as $line) {
            $parts = str_getcsv(trim($line));
            if (count($parts) >= 3) {
                $webinar->chatMessagesFake()->create([
                    'tenant_id' => $webinar->tenant_id,
                    'sender_name' => trim($parts[0]),
                    'message_text' => trim($parts[1]),
                    'display_at_seconds' => (int) trim($parts[2]),
                    'message_type' => $parts[3] ?? 'message',
                    'sort_order' => $count,
                ]);
                $count++;
            }
        }

        return back()->with('success', "{$count} správ bolo importovaných.");
    }

    public function updateConfig(Request $request, Webinar $webinar)
    {
        $validated = $request->validate([
            'viewer_count_min' => ['required', 'integer', 'min:0'],
            'viewer_count_max' => ['required', 'integer', 'min:1'],
        ]);

        $webinar->chatConfig()->updateOrCreate(
            ['webinar_id' => $webinar->id],
            array_merge($validated, ['tenant_id' => $webinar->tenant_id])
        );

        return back()->with('success', 'Konfigurácia chatu bola uložená.');
    }

    /**
     * Control room — see real messages from viewers
     */
    public function controlRoom(Webinar $webinar)
    {
        $messages = ChatMessageReal::where('webinar_id', $webinar->id)
            ->where('is_admin_reply', false)
            ->with('registrant')
            ->latest()
            ->paginate(50);

        $unreadCount = ChatMessageReal::where('webinar_id', $webinar->id)
            ->where('is_admin_reply', false)
            ->where('is_read_by_admin', false)
            ->count();

        return view('webinars.chat.control-room', compact('webinar', 'messages', 'unreadCount'));
    }

    public function replyToViewer(Request $request, Webinar $webinar)
    {
        $validated = $request->validate([
            'registrant_id' => ['required', 'integer'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        ChatMessageReal::create([
            'webinar_id' => $webinar->id,
            'tenant_id' => $webinar->tenant_id,
            'registrant_id' => $validated['registrant_id'],
            'session_id' => 'admin',
            'sender_name' => auth()->user()->name,
            'message_text' => $validated['message'],
            'is_admin_reply' => true,
            'reply_to_registrant_id' => $validated['registrant_id'],
            'is_read_by_admin' => true,
        ]);

        // Mark original messages from this registrant as read
        ChatMessageReal::where('webinar_id', $webinar->id)
            ->where('registrant_id', $validated['registrant_id'])
            ->where('is_admin_reply', false)
            ->update(['is_read_by_admin' => true]);

        return back()->with('success', 'Odpoveď bola odoslaná.');
    }
}
