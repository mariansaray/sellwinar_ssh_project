<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'webinar_id' => ['required', 'integer'],
            'session_id' => ['required', 'string', 'max:64'],
            'registrant_id' => ['nullable', 'integer'],
            'event_type' => ['required', 'string', 'max:50'],
            'event_data' => ['nullable', 'array'],
        ]);

        AnalyticsEvent::withoutGlobalScopes()->create([
            'tenant_id' => $this->getTenantIdFromWebinar($validated['webinar_id']),
            'webinar_id' => $validated['webinar_id'],
            'registrant_id' => $validated['registrant_id'] ?? null,
            'session_id' => $validated['session_id'],
            'event_type' => $validated['event_type'],
            'event_data' => $validated['event_data'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer_url' => $request->header('referer'),
            'device_type' => $this->detectDevice($request->userAgent()),
        ]);

        return response()->json(['ok' => true]);
    }

    private function getTenantIdFromWebinar(int $webinarId): ?int
    {
        return \App\Models\Webinar::withoutGlobalScopes()->where('id', $webinarId)->value('tenant_id');
    }

    private function detectDevice(?string $ua): string
    {
        if (!$ua) return 'desktop';
        $ua = strtolower($ua);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone')) return 'mobile';
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) return 'tablet';
        return 'desktop';
    }
}
