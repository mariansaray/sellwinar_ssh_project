@extends('layouts.dashboard')

@section('title', 'Diváci — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">Diváci</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">Diváci</h1>
            <p class="text-ink-500 dark:text-ink-400 mt-1">Všetci registrovaní diváci</p>
        </div>
        <a href="{{ route('dashboard.registrants.export', request()->query()) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-ink-200 dark:border-ink-600 text-sm text-ink-600 dark:text-ink-400 rounded-lg hover:bg-ink-50 dark:hover:bg-ink-600 transition-fast">
            <i data-lucide="download" class="w-4 h-4"></i> Export CSV
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative flex-1 sm:flex-none">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Hľadať meno alebo email..."
                   class="w-full sm:w-64 pl-10 pr-4 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
        </div>
        <select name="webinar_id" onchange="this.form.submit()"
                class="px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
            <option value="">Všetky webináre</option>
            @foreach($webinars as $w)
                <option value="{{ $w->id }}" {{ request('webinar_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()"
                class="px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
            <option value="">Všetky stavy</option>
            <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Registrovaný</option>
            <option value="attended" {{ request('status') === 'attended' ? 'selected' : '' }}>Zúčastnil sa</option>
            <option value="missed" {{ request('status') === 'missed' ? 'selected' : '' }}>Zmeškal</option>
            <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Konvertoval</option>
        </select>
    </form>

    @if($registrants->isEmpty())
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-12 text-center">
            <i data-lucide="users" class="w-12 h-12 text-ink-300 mx-auto mb-4"></i>
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-2">Žiadni diváci</h3>
            <p class="text-ink-500 text-sm">Keď sa niekto zaregistruje na webinár, objaví sa tu.</p>
        </div>
    @else
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-ink-50 dark:bg-ink-800">
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Meno</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Email</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Webinár</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Stav</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Dátum</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">UTM</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                        @foreach($registrants as $r)
                        <tr class="hover:bg-violet-50 dark:hover:bg-ink-600 transition-fast">
                            <td class="px-4 py-3">
                                <a href="{{ route('dashboard.registrants.show', $r) }}" class="text-sm font-medium text-ink-800 dark:text-white hover:text-violet-600">
                                    {{ $r->first_name }} {{ $r->last_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-ink-600 dark:text-ink-400">{{ $r->email }}</td>
                            <td class="px-4 py-3 text-sm text-ink-600 dark:text-ink-400">{{ $r->webinar->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @switch($r->status)
                                    @case('registered') <span class="inline-flex px-2.5 py-1 bg-info-50 text-info-700 text-xs font-medium rounded-full">Registrovaný</span> @break
                                    @case('attended') <span class="inline-flex px-2.5 py-1 bg-success-50 text-success-700 text-xs font-medium rounded-full">Zúčastnil sa</span> @break
                                    @case('missed') <span class="inline-flex px-2.5 py-1 bg-warning-50 text-warning-700 text-xs font-medium rounded-full">Zmeškal</span> @break
                                    @case('converted') <span class="inline-flex px-2.5 py-1 bg-violet-50 text-violet-700 text-xs font-medium rounded-full">Konvertoval</span> @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-sm text-ink-500">{{ $r->created_at->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs text-ink-400">{{ $r->utm_source ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $registrants->links() }}</div>
    @endif
@endsection
