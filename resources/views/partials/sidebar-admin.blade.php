@php $currentRoute = Route::currentRouteName(); @endphp

<div class="mb-4 px-4 py-2">
    <span class="inline-flex px-2.5 py-1 bg-violet-600 text-white text-[10px] font-bold tracking-wide uppercase rounded-full">Super Admin</span>
</div>

<div class="space-y-1">
    <a href="{{ route('super-admin.dashboard') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'super-admin.dashboard' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="shield" class="w-[18px] h-[18px]"></i>
        Dashboard
    </a>
    <a href="{{ route('super-admin.users.index') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ str_starts_with($currentRoute ?? '', 'super-admin.users') ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="users" class="w-[18px] h-[18px]"></i>
        Užívatelia
    </a>
    <a href="{{ route('super-admin.billing') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'super-admin.billing' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="credit-card" class="w-[18px] h-[18px]"></i>
        Billing prehľad
    </a>
    <a href="{{ route('super-admin.settings') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'super-admin.settings' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="settings" class="w-[18px] h-[18px]"></i>
        Globálne nastavenia
    </a>
    <a href="{{ route('super-admin.logs') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-fast {{ $currentRoute === 'super-admin.logs' ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 font-semibold' : 'text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700' }}">
        <i data-lucide="file-text" class="w-[18px] h-[18px]"></i>
        Systémové logy
    </a>
</div>

<div class="mt-6 border-t border-ink-200 dark:border-ink-600 pt-4">
    <p class="px-4 py-2 text-[11px] font-semibold tracking-[0.15em] uppercase text-ink-400">Rýchle odkazy</p>
    <a href="{{ route('dashboard.index') }}"
       class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-ink-500 dark:text-ink-400 hover:bg-ink-100 dark:hover:bg-ink-700 text-sm font-medium transition-fast">
        <i data-lucide="layout-dashboard" class="w-[18px] h-[18px]"></i>
        Užívateľský dashboard
    </a>
</div>
