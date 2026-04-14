<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'company' => ['required', 'string', 'max:255'],
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $validated['company'],
            'slug' => Str::slug($validated['company']) . '-' . Str::random(5),
            'plan' => 'trial',
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Create user
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'owner',
            'api_key' => Str::random(64),
        ]);

        // Send verification email
        $user->sendEmailVerificationNotification();

        Auth::login($user);

        return redirect()->route('dashboard.index')
            ->with('success', 'Vitajte v Sellwinar! Máte 14-dňový trial.');
    }
}
