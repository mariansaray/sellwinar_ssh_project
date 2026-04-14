<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BillingPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('tenant')
            ->where('role', '!=', 'super_admin')
            ->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->whereHas('tenant', fn ($q) => $q->where('subscription_status', $status));
        }

        $users = $query->paginate(25)->withQueryString();
        $plans = BillingPlan::where('is_active', true)->get();

        return view('super-admin.users.index', compact('users', 'plans'));
    }

    public function show(User $user)
    {
        $user->load('tenant');
        return view('super-admin.users.show', compact('user'));
    }

    public function toggleActive(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Nemôžete deaktivovať Super Admin účet.');
        }

        $tenant = $user->tenant;
        $newStatus = $tenant->subscription_status === 'canceled' ? 'active' : 'canceled';
        $tenant->update(['subscription_status' => $newStatus]);

        $action = $newStatus === 'canceled' ? 'deaktivovaný' : 'aktivovaný';
        return back()->with('success', "Účet {$user->name} bol {$action}.");
    }

    public function changePlan(Request $request, User $user)
    {
        $request->validate(['plan' => 'required|in:trial,monthly,yearly,lifetime']);

        $tenant = $user->tenant;
        $tenant->update([
            'plan' => $request->plan,
            'subscription_status' => $request->plan === 'trial' ? 'trialing' : 'active',
            'trial_ends_at' => $request->plan === 'trial' ? now()->addDays(14) : null,
        ]);

        return back()->with('success', "Plán pre {$user->name} bol zmenený na {$request->plan}.");
    }

    public function impersonate(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Nemôžete sa prihlásiť ako iný Super Admin.');
        }

        session(['impersonating' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('dashboard.index')->with('success', "Prihlásený ako {$user->name}.");
    }

    public function stopImpersonation()
    {
        $originalId = session('impersonating');

        if ($originalId) {
            session()->forget('impersonating');
            auth()->loginUsingId($originalId);
            return redirect()->route('super-admin.dashboard')->with('success', 'Vrátili ste sa do svojho účtu.');
        }

        return redirect()->route('super-admin.dashboard');
    }
}
