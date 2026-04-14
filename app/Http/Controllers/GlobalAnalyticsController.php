<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Registrant;
use App\Models\Webinar;
use Illuminate\Support\Facades\DB;

class GlobalAnalyticsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_registrations' => Registrant::count(),
            'registrations_today' => Registrant::whereDate('created_at', today())->count(),
            'registrations_week' => Registrant::where('created_at', '>=', now()->startOfWeek())->count(),
            'registrations_month' => Registrant::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_cta_clicks' => AnalyticsEvent::where('event_type', 'cta_click')->count(),
            'total_video_plays' => AnalyticsEvent::where('event_type', 'video_play')->count(),
        ];

        // Conversion rate
        $totalRegs = $stats['total_registrations'] ?: 1;
        $stats['conversion_rate'] = round(($stats['total_cta_clicks'] / $totalRegs) * 100, 1);

        // Registrations by day (30 days)
        $registrationsByDay = Registrant::where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Top webinars
        $topWebinars = Webinar::withCount('registrants')
            ->where('status', 'active')
            ->orderByDesc('registrants_count')
            ->limit(5)
            ->get();

        return view('analytics.index', compact('stats', 'registrationsByDay', 'topWebinars'));
    }
}
