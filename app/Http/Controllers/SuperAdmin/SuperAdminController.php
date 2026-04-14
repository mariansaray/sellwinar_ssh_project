<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BillingHistory;
use App\Models\NotificationLog;
use App\Models\Registrant;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Webinar;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', '!=', 'super_admin')->count(),
            'total_tenants' => Tenant::where('slug', '!=', 'sellwinar-platform')->count(),
            'active_subscriptions' => Tenant::where('subscription_status', 'active')->count(),
            'trialing' => Tenant::where('subscription_status', 'trialing')->count(),
            'total_webinars' => Webinar::withoutGlobalScopes()->count(),
            'total_registrants' => Registrant::withoutGlobalScopes()->count(),
        ];

        // MRR calculation (active monthly + yearly/12)
        $monthlyCount = Tenant::where('plan', 'monthly')->where('subscription_status', 'active')->count();
        $yearlyCount = Tenant::where('plan', 'yearly')->where('subscription_status', 'active')->count();
        $stats['mrr'] = ($monthlyCount * 39) + ($yearlyCount * 32.50);

        // New users last 30 days
        $newUsersByDay = User::where('role', '!=', 'super_admin')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return view('super-admin.dashboard', compact('stats', 'newUsersByDay'));
    }

    public function billing()
    {
        $history = BillingHistory::withoutGlobalScopes()->orderByDesc('created_at')->paginate(50);
        $mrr = $this->calculateMrr();

        $tenantsByPlan = Tenant::where('slug', '!=', 'sellwinar-platform')
            ->select('plan', DB::raw('COUNT(*) as count'))
            ->groupBy('plan')
            ->pluck('count', 'plan');

        return view('super-admin.billing', compact('history', 'mrr', 'tenantsByPlan'));
    }

    public function settings()
    {
        return view('super-admin.settings');
    }

    public function updateSettings(Request $request)
    {
        // Global settings stored in Sellwinar Platform tenant
        $tenant = Tenant::where('slug', 'sellwinar-platform')->first();
        $settings = $tenant->settings ?? [];

        $settings['stripe_key'] = $request->input('stripe_key', '');
        $settings['stripe_secret'] = $request->input('stripe_secret', '');
        $settings['default_smtp_host'] = $request->input('default_smtp_host', '');
        $settings['system_email'] = $request->input('system_email', '');
        $settings['maintenance_mode'] = $request->boolean('maintenance_mode');

        $tenant->update(['settings' => $settings]);

        return back()->with('success', 'Globálne nastavenia boli uložené.');
    }

    public function logs()
    {
        $emailLogs = NotificationLog::withoutGlobalScopes()->orderByDesc('created_at')->limit(50)->get();
        $webhookLogs = WebhookLog::withoutGlobalScopes()->orderByDesc('created_at')->limit(50)->get();

        return view('super-admin.logs', compact('emailLogs', 'webhookLogs'));
    }

    private function calculateMrr(): float
    {
        $monthlyCount = Tenant::where('plan', 'monthly')->where('subscription_status', 'active')->count();
        $yearlyCount = Tenant::where('plan', 'yearly')->where('subscription_status', 'active')->count();
        return ($monthlyCount * 39) + ($yearlyCount * 32.50);
    }
}
