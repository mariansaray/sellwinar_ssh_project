@extends('layouts.dashboard')

@section('title', $user->name . ' — Super Admin')

@section('breadcrumbs')
    <span class="text-violet-600 font-medium">Super Admin</span>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <a href="{{ route('super-admin.users.index') }}" class="hover:text-violet-600">Užívatelia</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">{{ $user->name }}</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $user->name }}</h1>
            <p class="text-ink-500 dark:text-ink-400 mt-1">{{ $user->email }}</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('super-admin.users.impersonate', $user) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Prihlásiť sa ako
                </button>
            </form>
            <form method="POST" action="{{ route('super-admin.users.toggle-active', $user) }}">
                @csrf
                <button type="submit" class="px-4 py-2 border border-ink-200 dark:border-ink-600 text-sm text-ink-600 dark:text-ink-400 rounded-lg hover:bg-ink-50 dark:hover:bg-ink-600 transition-fast">
                    {{ $user->tenant && $user->tenant->subscription_status === 'canceled' ? 'Aktivovať' : 'Deaktivovať' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- User info --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Informácie</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-ink-400">Rola</dt><dd class="font-medium text-ink-800 dark:text-white">{{ $user->role }}</dd></div>
                <div><dt class="text-ink-400">Registrovaný</dt><dd class="text-ink-800 dark:text-white">{{ $user->created_at->format('d.m.Y H:i') }}</dd></div>
                <div><dt class="text-ink-400">Posledné prihlásenie</dt><dd class="text-ink-800 dark:text-white">{{ $user->last_login_at?->format('d.m.Y H:i') ?? '—' }}</dd></div>
                <div><dt class="text-ink-400">Email overený</dt><dd class="text-ink-800 dark:text-white">{{ $user->email_verified_at ? 'Áno' : 'Nie' }}</dd></div>
            </dl>
        </div>

        {{-- Tenant info --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Firma / Tenant</h3>
            @if($user->tenant)
            <dl class="space-y-3 text-sm">
                <div><dt class="text-ink-400">Názov</dt><dd class="font-medium text-ink-800 dark:text-white">{{ $user->tenant->name }}</dd></div>
                <div><dt class="text-ink-400">Plán</dt><dd>
                    <span class="inline-flex px-2.5 py-1 bg-violet-50 dark:bg-violet-900/20 text-violet-700 text-xs font-medium rounded-full">{{ $user->tenant->plan }}</span>
                </dd></div>
                <div><dt class="text-ink-400">Stav</dt><dd>
                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $user->tenant->isActive() ? 'bg-success-50 text-success-700' : 'bg-danger-50 text-danger-700' }}">
                        {{ $user->tenant->subscription_status }}
                    </span>
                </dd></div>
                @if($user->tenant->trial_ends_at)
                <div><dt class="text-ink-400">Trial do</dt><dd class="text-ink-800 dark:text-white">{{ $user->tenant->trial_ends_at->format('d.m.Y') }}</dd></div>
                @endif
            </dl>

            {{-- Change plan --}}
            <form method="POST" action="{{ route('super-admin.users.change-plan', $user) }}" class="mt-4 flex items-center gap-2">
                @csrf
                <select name="plan" class="px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    @foreach(['trial', 'monthly', 'yearly', 'lifetime'] as $plan)
                        <option value="{{ $plan }}" {{ $user->tenant->plan === $plan ? 'selected' : '' }}>{{ ucfirst($plan) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Zmeniť</button>
            </form>
            @else
                <p class="text-sm text-ink-400">Žiadny tenant.</p>
            @endif
        </div>

        {{-- Stats --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Štatistiky</h3>
            @php
                $webinarCount = \App\Models\Webinar::withoutGlobalScopes()->where('tenant_id', $user->tenant_id)->count();
                $registrantCount = \App\Models\Registrant::withoutGlobalScopes()->where('tenant_id', $user->tenant_id)->count();
            @endphp
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-ink-400">Webináre</dt>
                    <dd class="font-heading text-xl font-bold text-ink-800 dark:text-white">{{ $webinarCount }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-ink-400">Registrácie</dt>
                    <dd class="font-heading text-xl font-bold text-ink-800 dark:text-white">{{ $registrantCount }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Webinars list --}}
    <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
        <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Webináre tohto užívateľa</h3>
        @php $webinars = \App\Models\Webinar::withoutGlobalScopes()->where('tenant_id', $user->tenant_id)->latest()->get(); @endphp
        @if($webinars->isEmpty())
            <p class="text-sm text-ink-400">Žiadne webináre.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-ink-50 dark:bg-ink-800">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Názov</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Typ</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Stav</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Vytvorený</th>
                    </tr></thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                        @foreach($webinars as $w)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-ink-800 dark:text-white">{{ $w->name }}</td>
                            <td class="px-4 py-3 text-xs"><span class="px-2.5 py-1 bg-violet-50 text-violet-700 rounded-full">{{ $w->type }}</span></td>
                            <td class="px-4 py-3 text-xs"><span class="px-2.5 py-1 rounded-full {{ $w->status === 'active' ? 'bg-success-50 text-success-700' : 'bg-ink-100 text-ink-500' }}">{{ $w->status }}</span></td>
                            <td class="px-4 py-3 text-sm text-ink-500">{{ $w->created_at->format('d.m.Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
