@extends('layouts.dashboard')

@section('title', 'Globálne nastavenia — Super Admin')

@section('breadcrumbs')
    <span class="text-violet-600 font-medium">Super Admin</span>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Globálne nastavenia</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Globálne nastavenia</h1>

    @php
        $platformTenant = \App\Models\Tenant::where('slug', 'sellwinar-platform')->first();
        $settings = $platformTenant->settings ?? [];
    @endphp

    <form method="POST" action="{{ route('super-admin.settings.update') }}" class="max-w-2xl space-y-6">
        @csrf @method('PUT')

        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Stripe</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Stripe Public Key</label>
                    <input type="text" name="stripe_key" value="{{ $settings['stripe_key'] ?? '' }}" placeholder="pk_live_..." class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Stripe Secret Key</label>
                    <input type="password" name="stripe_secret" value="{{ $settings['stripe_secret'] ?? '' }}" placeholder="sk_live_..." class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Default SMTP</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">SMTP Host</label>
                    <input type="text" name="default_smtp_host" value="{{ $settings['default_smtp_host'] ?? '' }}" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Systémový e-mail</label>
                    <input type="email" name="system_email" value="{{ $settings['system_email'] ?? '' }}" placeholder="system@sellwinar.com" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Systém</h3>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="maintenance_mode" value="0">
                <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                <div>
                    <span class="text-sm font-medium text-ink-800 dark:text-white">Režim údržby</span>
                    <p class="text-xs text-ink-400">Keď je zapnutý, bežní užívatelia uvidia stránku "Údržba".</p>
                </div>
            </label>
        </div>

        <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            Uložiť nastavenia
        </button>
    </form>
@endsection
