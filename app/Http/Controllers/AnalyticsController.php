<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Registrant;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function webinar(Webinar $webinar)
    {
        $tenantId = app()->bound('current_tenant_id') ? app('current_tenant_id') : $webinar->tenant_id;

        // Funnel data
        $pageViews = AnalyticsEvent::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->where('event_type', 'page_view')
            ->count();

        $registrations = Registrant::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->count();

        $attended = Registrant::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->whereIn('status', ['attended', 'converted'])
            ->count();

        $ctaClicks = AnalyticsEvent::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->where('event_type', 'cta_click')
            ->count();

        $funnel = [
            ['label' => 'Návštevy reg. stránky', 'count' => $pageViews],
            ['label' => 'Registrácie', 'count' => $registrations],
            ['label' => 'Účasť', 'count' => $attended],
            ['label' => 'CTA kliknutia', 'count' => $ctaClicks],
        ];

        // Registrations by day (last 30 days)
        $registrationsByDay = Registrant::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // UTM sources
        $utmSources = Registrant::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->whereNotNull('utm_source')
            ->select('utm_source', DB::raw('COUNT(*) as count'))
            ->groupBy('utm_source')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Device breakdown
        $devices = AnalyticsEvent::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->where('event_type', 'registration')
            ->whereNotNull('device_type')
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->get();

        // Average watch time
        $avgWatchTime = AnalyticsEvent::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->where('event_type', 'video_complete')
            ->avg(DB::raw("CAST(JSON_EXTRACT(event_data, '$.seconds_watched') AS UNSIGNED)")) ?? 0;

        return view('webinars.analytics', compact(
            'webinar', 'funnel', 'registrationsByDay', 'utmSources', 'devices', 'avgWatchTime'
        ));
    }

    public function exportCsv(Webinar $webinar)
    {
        $registrants = Registrant::withoutGlobalScopes()
            ->where('webinar_id', $webinar->id)
            ->orderBy('created_at')
            ->get();

        $filename = 'registrants-' . $webinar->slug . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($registrants) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Meno', 'Priezvisko', 'Email', 'Telefón', 'Stav', 'Dátum registrácie', 'Naplánovaný čas', 'UTM Source', 'UTM Medium', 'UTM Campaign']);

            foreach ($registrants as $r) {
                fputcsv($out, [
                    $r->first_name, $r->last_name, $r->email, $r->phone,
                    $r->status, $r->created_at->format('Y-m-d H:i'),
                    $r->scheduled_at?->format('Y-m-d H:i'),
                    $r->utm_source, $r->utm_medium, $r->utm_campaign,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
