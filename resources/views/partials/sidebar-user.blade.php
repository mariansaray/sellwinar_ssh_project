@php $currentRoute = Route::currentRouteName(); @endphp

<div class="space-y-1">
    <a href="{{ route('dashboard.index') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'dashboard.index' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="layout-dashboard" class="w-[18px] h-[18px]"></i>
        Dashboard
    </a>
    <a href="{{ route('dashboard.webinars.index') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ str_starts_with($currentRoute ?? '', 'dashboard.webinars') ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="video" class="w-[18px] h-[18px]"></i>
        Webináre
    </a>
    <a href="{{ route('dashboard.smart-videos.index') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'dashboard.smart-videos.index' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="play-circle" class="w-[18px] h-[18px]"></i>
        Smart videá
    </a>
    <a href="{{ route('dashboard.registrants.index') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ str_starts_with($currentRoute ?? '', 'dashboard.registrants') ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="users" class="w-[18px] h-[18px]"></i>
        Diváci
    </a>
    <a href="{{ route('dashboard.analytics.index') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'dashboard.analytics.index' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="bar-chart-3" class="w-[18px] h-[18px]"></i>
        Analytika
    </a>
</div>

<div class="mt-6">
    <p class="px-4 py-2 text-[11px] font-semibold tracking-[0.15em] uppercase text-ink-400">Komunikácia</p>
    <div class="space-y-1">
        <a href="{{ route('dashboard.email-templates.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'dashboard.email-templates.index' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
            <i data-lucide="mail" class="w-[18px] h-[18px]"></i>
            E-mail šablóny
        </a>
        <a href="{{ route('dashboard.sms-templates.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'dashboard.sms-templates.index' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
            <i data-lucide="smartphone" class="w-[18px] h-[18px]"></i>
            SMS šablóny
        </a>
    </div>
</div>

<div class="mt-6">
    <p class="px-4 py-2 text-[11px] font-semibold tracking-[0.15em] uppercase text-ink-400">Nastavenia</p>
    <div class="space-y-1">
        <a href="{{ route('dashboard.settings.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ str_starts_with($currentRoute ?? '', 'dashboard.settings') ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
            <i data-lucide="settings" class="w-[18px] h-[18px]"></i>
            Nastavenia
        </a>
        <a href="{{ route('dashboard.billing.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'dashboard.billing.index' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
            <i data-lucide="credit-card" class="w-[18px] h-[18px]"></i>
            Billing
        </a>
    </div>
</div>

@if(Auth::user()->isSuperAdmin())
<div class="mt-6 border-t border-ink-200 dark:border-ink-600 pt-4">
    <a href="{{ route('super-admin.dashboard') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-violet-600 text-white text-sm font-semibold transition-fast hover:bg-violet-500">
        <i data-lucide="shield" class="w-[18px] h-[18px]"></i>
        Super Admin panel
    </a>
</div>
@endif
