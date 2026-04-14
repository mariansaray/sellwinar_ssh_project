@extends('layouts.dashboard')

@section('title', 'Užívatelia — Super Admin — Sellwinar')

@section('breadcrumbs')
    <span class="text-violet-600 font-medium">Super Admin</span>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Užívatelia</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">Užívatelia</h1>
        <form method="GET" class="flex items-center gap-3">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Hľadať..."
                       class="pl-10 pr-4 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
            </div>
            <select name="status" onchange="this.form.submit()"
                    class="px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                <option value="">Všetky stavy</option>
                <option value="trialing" {{ request('status') === 'trialing' ? 'selected' : '' }}>Trial</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktívny</option>
                <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Zrušený</option>
            </select>
        </form>
    </div>

    <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-ink-50 dark:bg-ink-800">
                        <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Meno</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">E-mail</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Firma</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Plán</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Stav</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Akcie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                    @forelse($users as $user)
                    <tr class="hover:bg-violet-50 dark:hover:bg-ink-600 transition-fast">
                        <td class="px-4 py-3 text-sm font-medium text-ink-800 dark:text-white">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-sm text-ink-600 dark:text-ink-400">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-sm text-ink-600 dark:text-ink-400">{{ $user->tenant->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex px-2.5 py-1 bg-violet-50 dark:bg-violet-900/20 text-violet-700 text-xs font-medium rounded-full">
                                {{ $user->tenant->plan ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($user->tenant && $user->tenant->subscription_status === 'active')
                                <span class="inline-flex px-2.5 py-1 bg-success-50 text-success-700 text-xs font-medium rounded-full">Aktívny</span>
                            @elseif($user->tenant && $user->tenant->subscription_status === 'trialing')
                                <span class="inline-flex px-2.5 py-1 bg-info-50 text-info-700 text-xs font-medium rounded-full">Trial</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 bg-danger-50 text-danger-700 text-xs font-medium rounded-full">Zrušený</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <form method="POST" action="{{ route('super-admin.users.impersonate', $user) }}">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-600 transition-fast" title="Prihlásiť sa ako">
                                        <i data-lucide="log-in" class="w-4 h-4 text-ink-500"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('super-admin.users.toggle-active', $user) }}">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-600 transition-fast"
                                            title="{{ $user->tenant && $user->tenant->subscription_status === 'canceled' ? 'Aktivovať' : 'Deaktivovať' }}">
                                        <i data-lucide="{{ $user->tenant && $user->tenant->subscription_status === 'canceled' ? 'check-circle' : 'x-circle' }}" class="w-4 h-4 text-ink-500"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-ink-400">Žiadni užívatelia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
