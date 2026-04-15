@extends('layouts.dashboard')

@section('title', 'E-mail šablóny — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">E-mail šablóny</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">E-mail šablóny</h1>
            <p class="text-ink-500 dark:text-ink-400 mt-1">Knižnica predpripravených šablón. Vytvorte si šablóny a potom ich importujte do webinárov.</p>
        </div>
        <a href="{{ route('dashboard.email-templates.create') }}"
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nová šablóna
        </a>
    </div>

    {{-- Placeholders help --}}
    <div class="mb-6 p-4 bg-ink-50 dark:bg-ink-800 rounded-xl border border-ink-200 dark:border-ink-600">
        <p class="text-sm text-ink-600 dark:text-ink-400">
            <strong>Dostupné placeholdery:</strong>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{meno}}</code>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{email}}</code>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{nazov_webinara}}</code>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{datum_webinara}}</code>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{cas_webinara}}</code>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{link_na_webinar}}</code>
        </p>
    </div>

    @if($templates->isEmpty())
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-12 text-center">
            <i data-lucide="mail" class="w-12 h-12 text-ink-300 dark:text-ink-500 mx-auto mb-4"></i>
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-2">Žiadne šablóny</h3>
            <p class="text-ink-500 dark:text-ink-400 text-sm mb-6">Vytvorte si prvú e-mail šablónu — potom ju budete môcť použiť v akomkoľvek webinári.</p>
            <a href="{{ route('dashboard.email-templates.create') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">
                <i data-lucide="plus" class="w-4 h-4"></i> Vytvoriť prvú šablónu
            </a>
        </div>
    @else
        @foreach($grouped as $triggerType => $group)
        <div class="mb-6">
            <h2 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-3">{{ $triggerLabels[$triggerType] ?? $triggerType }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($group as $tpl)
                <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-5 shadow-sm hover:shadow-md transition-fast">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-medium text-sm text-ink-800 dark:text-white">{{ $tpl->name }}</h3>
                            <p class="text-xs text-ink-400 mt-0.5">{{ $tpl->delay_minutes > 0 ? '+' . $tpl->delay_minutes : $tpl->delay_minutes }} min</p>
                        </div>
                        @if($tpl->is_default)
                            <span class="inline-flex px-2 py-0.5 bg-violet-50 dark:bg-violet-900/20 text-violet-700 text-[10px] font-semibold rounded-full">Predvolená</span>
                        @endif
                    </div>
                    <p class="text-xs text-ink-500 dark:text-ink-400 mb-4 line-clamp-2">Predmet: {{ $tpl->subject }}</p>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard.email-templates.edit', $tpl) }}" class="px-3 py-1.5 text-xs text-violet-600 hover:text-violet-500 border border-violet-200 dark:border-violet-800 rounded-lg hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-fast">Upraviť</a>

                        {{-- Apply to webinar dropdown --}}
                        @php $webinars = \App\Models\Webinar::select('id', 'name')->get(); @endphp
                        @if($webinars->isNotEmpty())
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="px-3 py-1.5 text-xs text-ink-600 dark:text-ink-400 border border-ink-200 dark:border-ink-600 rounded-lg hover:bg-ink-50 dark:hover:bg-ink-600 transition-fast">
                                Použiť vo webinári
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute left-0 mt-1 w-56 bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl shadow-lg py-1 z-50">
                                @foreach($webinars as $w)
                                <form method="POST" action="{{ route('dashboard.email-templates.apply', $tpl) }}">
                                    @csrf
                                    <input type="hidden" name="webinar_id" value="{{ $w->id }}">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-ink-600 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-600">{{ $w->name }}</button>
                                </form>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(!$tpl->is_default)
                        <form method="POST" action="{{ route('dashboard.email-templates.destroy', $tpl) }}" onsubmit="return confirm('Zmazať šablónu?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 text-xs text-danger-600 border border-danger-200 dark:border-danger-800 rounded-lg hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-fast">Zmazať</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @endif
@endsection
