<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->tenant) {
            return redirect()->route('login');
        }

        // Super admins bypass subscription check
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = $user->tenant;

        if (!$tenant->isActive()) {
            return redirect()->route('billing.plans')
                ->with('warning', 'Vaše predplatné vypršalo. Vyberte si plán pre pokračovanie.');
        }

        return $next($request);
    }
}
