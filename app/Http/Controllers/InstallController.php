<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class InstallController extends Controller
{
    public function index()
    {
        // If already installed, redirect
        if ($this->isInstalled()) {
            return redirect('/login')->with('info', 'Aplikácia je už nainštalovaná.');
        }

        return view('install');
    }

    public function run(Request $request)
    {
        if ($this->isInstalled()) {
            return redirect('/login')->with('info', 'Aplikácia je už nainštalovaná.');
        }

        try {
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Run seeders
            Artisan::call('db:seed', ['--force' => true]);

            return redirect('/login')->with('success', 'Inštalácia prebehla úspešne! Prihláste sa ako admin@sellwinar.com / admin123');
        } catch (\Exception $e) {
            return back()->with('error', 'Chyba pri inštalácii: ' . $e->getMessage());
        }
    }

    private function isInstalled(): bool
    {
        try {
            return Schema::hasTable('users') && User::where('role', 'super_admin')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}
