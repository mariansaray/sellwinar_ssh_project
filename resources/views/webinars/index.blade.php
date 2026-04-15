@extends('layouts.dashboard')

@section('title', 'Webináre — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">Webináre</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">Webináre</h1>
            <p class="text-ink-500 dark:text-ink-400 mt-1">Správa vašich webinárov a smart videí</p>
        </div>
        <a href="{{ route('dashboard.webinars.create') }}"
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nový webinár
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:flex-none">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Hľadať..."
                       class="w-full sm:w-64 pl-10 pr-4 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
            </div>
            <select name="type" onchange="this.form.submit()"
                    class="px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                <option value="">Všetky typy</option>
                <option value="evergreen" {{ request('type') === 'evergreen' ? 'selected' : '' }}>Evergreen</option>
                <option value="smart_video" {{ request('type') === 'smart_video' ? 'selected' : '' }}>Smart video</option>
            </select>
            <select name="status" onchange="this.form.submit()"
                    class="px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                <option value="">Všetky stavy</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Koncept</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktívny</option>
                <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Pozastavený</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archivovaný</option>
            </select>
        </form>
    </div>

    {{-- Table --}}
    @if($webinars->isEmpty())
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-12 text-center">
            <i data-lucide="video" class="w-12 h-12 text-ink-300 dark:text-ink-500 mx-auto mb-4"></i>
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-2">Žiadne webináre</h3>
            <p class="text-ink-500 dark:text-ink-400 text-sm mb-6">Vytvorte svoj prvý webinár a začnite zbierať registrácie.</p>
            <a href="{{ route('dashboard.webinars.create') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg shadow hover:shadow-violet transition-fast">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Vytvoriť prvý webinár
            </a>
        </div>
    @else
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-ink-50 dark:bg-ink-800">
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Názov</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Typ</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Stav</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Registrácie</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Vytvorený</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold tracking-wide uppercase text-ink-500">Akcie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                        @foreach($webinars as $webinar)
                        <tr class="hover:bg-violet-50 dark:hover:bg-ink-600 transition-fast">
                            <td class="px-4 py-3">
                                <a href="{{ route('dashboard.webinars.edit', $webinar) }}" class="text-sm font-medium text-ink-800 dark:text-white hover:text-violet-600">
                                    {{ $webinar->name }}
                                </a>
                                <p class="text-xs text-ink-400 mt-0.5">/{{ $webinar->slug }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($webinar->isEvergreen())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300 text-xs font-medium rounded-full">
                                        <i data-lucide="video" class="w-3 h-3"></i> Evergreen
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-info-50 dark:bg-info-900/20 text-info-700 dark:text-info-300 text-xs font-medium rounded-full">
                                        <i data-lucide="play-circle" class="w-3 h-3"></i> Smart video
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @switch($webinar->status)
                                    @case('active')
                                        <span class="inline-flex px-2.5 py-1 bg-success-50 text-success-700 text-xs font-medium rounded-full">Aktívny</span>
                                        @break
                                    @case('paused')
                                        <span class="inline-flex px-2.5 py-1 bg-warning-50 text-warning-700 text-xs font-medium rounded-full">Pozastavený</span>
                                        @break
                                    @case('draft')
                                        <span class="inline-flex px-2.5 py-1 bg-ink-100 text-ink-600 text-xs font-medium rounded-full">Koncept</span>
                                        @break
                                    @case('archived')
                                        <span class="inline-flex px-2.5 py-1 bg-ink-100 text-ink-400 text-xs font-medium rounded-full">Archivovaný</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-sm text-ink-600 dark:text-ink-400">
                                {{ $webinar->registrants_count }}
                            </td>
                            <td class="px-4 py-3 text-sm text-ink-500 dark:text-ink-400">
                                {{ $webinar->created_at->format('d.m.Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1" x-data="{ open: false }">
                                    <a href="{{ route('dashboard.webinars.edit', $webinar) }}"
                                       class="p-2 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-600 transition-fast" title="Upraviť">
                                        <i data-lucide="pencil" class="w-4 h-4 text-ink-500"></i>
                                    </a>
                                    <div class="relative">
                                        <button @click="open = !open" class="p-2 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-600 transition-fast">
                                            <i data-lucide="more-vertical" class="w-4 h-4 text-ink-500"></i>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" x-transition
                                             class="absolute right-0 mt-1 w-44 bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl shadow-lg py-1 z-50">
                                            <form method="POST" action="{{ route('dashboard.webinars.toggle-status', $webinar) }}">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-ink-600 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-600">
                                                    {{ $webinar->status === 'active' ? 'Pozastaviť' : 'Aktivovať' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('dashboard.webinars.duplicate', $webinar) }}">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-ink-600 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-600">
                                                    Duplikovať
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('dashboard.webinars.destroy', $webinar) }}"
                                                  onsubmit="return confirm('Naozaj chcete zmazať tento webinár?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-danger-600 hover:bg-ink-50 dark:hover:bg-ink-600">
                                                    Zmazať
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $webinars->links() }}
        </div>
    @endif
@endsection
