<?php

namespace App\Http\Controllers;

use App\Models\BillingHistory;
use App\Models\BillingPlan;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $plans = BillingPlan::where('is_active', true)->orderBy('price')->get();
        $history = BillingHistory::orderByDesc('created_at')->limit(20)->get();
        $currentPlan = $plans->firstWhere('slug', $tenant->plan);

        return view('billing.index', compact('tenant', 'plans', 'history', 'currentPlan'));
    }
}
