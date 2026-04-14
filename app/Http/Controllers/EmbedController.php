<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Registrant;
use App\Models\Tenant;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmbedController extends Controller
{
    /**
     * Embeddable registration form (iframe)
     */
    public function registerForm(string $tenantSlug, string $webinarSlug)
    {
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();
        $webinar = Webinar::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('slug', $webinarSlug)
            ->where('status', 'active')
            ->firstOrFail();

        $config = $webinar->registration_page_config ?? [];

        return response()
            ->view('embed.register', compact('tenant', 'webinar', 'config'))
            ->header('X-Frame-Options', 'ALLOWALL')
            ->header('Content-Security-Policy', "frame-ancestors *");
    }

    /**
     * Handle embed registration submit
     */
    public function registerSubmit(Request $request, string $tenantSlug, string $webinarSlug)
    {
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();
        $webinar = Webinar::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('slug', $webinarSlug)
            ->firstOrFail();

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $schedule = $webinar->schedule;
        $scheduledAt = $schedule
            ? ($schedule->schedule_type === 'jit'
                ? now()->addMinutes($schedule->jit_delay_minutes ?? 15)
                : now()->addHours($schedule->interval_hours ?? 2))
            : now()->addMinutes(15);

        $registrant = Registrant::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'webinar_id' => $webinar->id,
            'email' => $validated['email'],
            'first_name' => $validated['first_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'utm_source' => $request->get('utm_source'),
            'utm_medium' => $request->get('utm_medium'),
            'utm_campaign' => $request->get('utm_campaign'),
            'scheduled_at' => $scheduledAt,
            'access_token' => Str::random(64),
            'status' => 'registered',
            'registration_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        AnalyticsEvent::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'webinar_id' => $webinar->id,
            'registrant_id' => $registrant->id,
            'session_id' => Str::random(32),
            'event_type' => 'registration',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer_url' => $request->header('referer'),
        ]);

        $redirectUrl = route('public.thankyou', $registrant->access_token);

        // For embed: show success in iframe or redirect parent
        $embedBehavior = $webinar->registration_page_config['embed_behavior'] ?? 'redirect';

        if ($embedBehavior === 'success_message') {
            return response()
                ->view('embed.register-success', compact('webinar', 'registrant'))
                ->header('X-Frame-Options', 'ALLOWALL')
                ->header('Content-Security-Policy', "frame-ancestors *");
        }

        return response()
            ->view('embed.register-redirect', compact('redirectUrl', 'webinar'))
            ->header('X-Frame-Options', 'ALLOWALL')
            ->header('Content-Security-Policy', "frame-ancestors *");
    }

    /**
     * Embeddable smart video player (iframe)
     */
    public function player(int $webinarId)
    {
        $webinar = Webinar::withoutGlobalScopes()
            ->where('id', $webinarId)
            ->where('status', 'active')
            ->firstOrFail();

        $playerConfig = array_merge($webinar->player_config ?? [], [
            'source' => $webinar->video_source,
            'videoUrl' => $webinar->video_url,
        ]);
        $ctaConfig = $webinar->cta_config ?? [];

        return response()
            ->view('embed.player', compact('webinar', 'playerConfig', 'ctaConfig'))
            ->header('X-Frame-Options', 'ALLOWALL')
            ->header('Content-Security-Policy', "frame-ancestors *");
    }
}
