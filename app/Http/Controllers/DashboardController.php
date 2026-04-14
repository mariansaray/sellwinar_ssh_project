<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use App\Models\Webinar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;

        $stats = [
            'active_webinars' => Webinar::where('status', 'active')->where('type', 'evergreen')->count(),
            'active_smart_videos' => Webinar::where('status', 'active')->where('type', 'smart_video')->count(),
            'registrations_today' => Registrant::whereDate('created_at', today())->count(),
            'registrations_week' => Registrant::where('created_at', '>=', now()->startOfWeek())->count(),
            'registrations_month' => Registrant::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_registrations' => Registrant::count(),
        ];

        return view('dashboard.index', compact('tenant', 'stats'));
    }
}
