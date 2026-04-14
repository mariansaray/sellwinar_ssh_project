<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Registrant;
use App\Models\Tenant;
use App\Models\Webinar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicWebinarController extends Controller
{
    /**
     * Registration page: /{tenant-slug}/w/{webinar-slug}
     */
    public function registrationPage(string $tenantSlug, string $webinarSlug)
    {
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();
        $webinar = Webinar::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('slug', $webinarSlug)
            ->where('status', 'active')
            ->where('type', 'evergreen')
            ->firstOrFail();

        $registrantCount = Registrant::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->count();

        $config = $webinar->registration_page_config ?? [];
        $schedule = $webinar->schedule;

        // Calculate next available time for display
        $nextTime = $this->calculateNextTime($schedule);

        return view('public.register', compact('tenant', 'webinar', 'config', 'registrantCount', 'nextTime'));
    }

    /**
     * Handle registration form submission
     */
    public function register(Request $request, string $tenantSlug, string $webinarSlug)
    {
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();
        $webinar = Webinar::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('slug', $webinarSlug)
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $schedule = $webinar->schedule;
        $scheduledAt = $this->calculateScheduledAt($schedule);

        $registrant = Registrant::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'webinar_id' => $webinar->id,
            'email' => $validated['email'],
            'first_name' => $validated['first_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'utm_source' => $request->get('utm_source'),
            'utm_medium' => $request->get('utm_medium'),
            'utm_campaign' => $request->get('utm_campaign'),
            'utm_term' => $request->get('utm_term'),
            'utm_content' => $request->get('utm_content'),
            'scheduled_at' => $scheduledAt,
            'access_token' => Str::random(64),
            'status' => 'registered',
            'registration_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Track registration event
        AnalyticsEvent::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'webinar_id' => $webinar->id,
            'registrant_id' => $registrant->id,
            'session_id' => $request->cookie('sellwinar_session', Str::random(32)),
            'event_type' => 'registration',
            'event_data' => ['utm_source' => $request->get('utm_source')],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer_url' => $request->header('referer'),
            'device_type' => $this->detectDevice($request->userAgent()),
        ]);

        return redirect()->route('public.thankyou', ['accessToken' => $registrant->access_token]);
    }

    /**
     * Thank you page: /thankyou/{access_token}
     */
    public function thankYouPage(string $accessToken)
    {
        $registrant = Registrant::withoutGlobalScopes()
            ->where('access_token', $accessToken)
            ->firstOrFail();

        $webinar = Webinar::withoutGlobalScopes()->find($registrant->webinar_id);
        $config = $webinar->thankyou_page_config ?? [];

        // Google Calendar link
        $gcalUrl = $this->generateGoogleCalendarUrl($webinar, $registrant);

        return view('public.thankyou', compact('registrant', 'webinar', 'config', 'gcalUrl'));
    }

    /**
     * Webinar room: /watch/{access_token}
     */
    public function watchPage(string $accessToken)
    {
        $registrant = Registrant::withoutGlobalScopes()
            ->where('access_token', $accessToken)
            ->firstOrFail();

        $webinar = Webinar::withoutGlobalScopes()->find($registrant->webinar_id);
        $now = now();

        // Check if webinar window is valid (4 hours after scheduled_at)
        $windowEnd = $registrant->scheduled_at->copy()->addHours(4);

        if ($now->lt($registrant->scheduled_at)) {
            // Before scheduled time — show countdown
            return view('public.watch-countdown', compact('registrant', 'webinar'));
        }

        if ($now->gt($windowEnd)) {
            // After window — expired
            return view('public.watch-expired', compact('registrant', 'webinar'));
        }

        // Mark as attended
        if ($registrant->status === 'registered') {
            $registrant->update(['status' => 'attended']);
        }

        $playerConfig = array_merge($webinar->player_config ?? [], [
            'source' => $webinar->video_source,
            'videoUrl' => $webinar->video_url,
        ]);

        $chatConfig = $webinar->chatConfig;
        $ctaConfig = $webinar->cta_config ?? [];

        return view('public.watch', compact('registrant', 'webinar', 'playerConfig', 'chatConfig', 'ctaConfig'));
    }

    // ---- Private helpers ----

    private function calculateNextTime($schedule): ?Carbon
    {
        if (!$schedule) return now()->addMinutes(15);

        return match ($schedule->schedule_type) {
            'jit' => now()->addMinutes($schedule->jit_delay_minutes ?? 15),
            'interval' => now()->addHours($schedule->interval_hours ?? 2)->startOfHour(),
            default => now()->addMinutes(15),
        };
    }

    private function calculateScheduledAt($schedule): Carbon
    {
        if (!$schedule) return now()->addMinutes(15);

        return match ($schedule->schedule_type) {
            'jit' => now()->addMinutes($schedule->jit_delay_minutes ?? 15),
            'interval' => now()->addHours($schedule->interval_hours ?? 2)->startOfHour(),
            default => now()->addMinutes(15),
        };
    }

    private function generateGoogleCalendarUrl(Webinar $webinar, Registrant $registrant): string
    {
        $start = $registrant->scheduled_at->format('Ymd\THis\Z');
        $end = $registrant->scheduled_at->copy()->addHours(2)->format('Ymd\THis\Z');
        $title = urlencode($webinar->name);
        $details = urlencode("Odkaz na webinár: " . route('public.watch', $registrant->access_token));

        return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$start}/{$end}&details={$details}";
    }

    private function detectDevice(?string $ua): string
    {
        if (!$ua) return 'desktop';
        $ua = strtolower($ua);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone') || str_contains($ua, 'android')) return 'mobile';
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) return 'tablet';
        return 'desktop';
    }
}
