@extends('layouts.dashboard')

@section('title', 'SMS šablóny — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">SMS šablóny</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">SMS šablóny</h1>
            <p class="text-ink-500 dark:text-ink-400 mt-1">Knižnica SMS šablón. Najprv nastavte Twilio v <a href="{{ route('dashboard.settings.index') }}?tab=twilio" class="text-violet-600 hover:text-violet-500">Nastaveniach</a>.</p>
        </div>
        <a href="{{ route('dashboard.sms-templates.create') }}"
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nová SMS šablóna
        </a>
    </div>

    <div class="mb-6 p-4 bg-ink-50 dark:bg-ink-800 rounded-xl border border-ink-200 dark:border-ink-600">
        <p class="text-sm text-ink-600 dark:text-ink-400">
            <strong>Placeholdery:</strong>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{meno}}</code>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{nazov_webinara}}</code>
            <code class="text-xs bg-white dark:bg-ink-700 px-1.5 py-0.5 rounded mx-0.5">@{{link_na_webinar}}</code>
            | Max 160 znakov na správu.
        </p>
    </div>

    @if($templates->isEmpty())
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-12 text-center">
            <i data-lucide="smartphone" class="w-12 h-12 text-ink-300 dark:text-ink-500 mx-auto mb-4"></i>
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-2">Žiadne SMS šablóny</h3>
            <p class="text-ink-500 text-sm mb-6">Vytvorte si SMS šablónu a potom ju použite v akomkoľvek webinári.</p>
            <a href="{{ route('dashboard.sms-templates.create') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">
                <i data-lucide="plus" class="w-4 h-4"></i> Vytvoriť prvú šablónu
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $tpl)
            <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-5 shadow-sm hover:shadow-md transition-fast">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-medium text-sm text-ink-800 dark:text-white">{{ $tpl->name }}</h3>
                    <span class="inline-flex px-2 py-0.5 bg-ink-100 dark:bg-ink-600 text-ink-500 text-[10px] font-medium rounded-full">{{ $triggerLabels[$tpl->trigger_type] ?? $tpl->trigger_type }}</span>
                </div>
                <p class="text-xs text-ink-500 dark:text-ink-400 mb-3 line-clamp-2">{{ $tpl->message_text }}</p>
                <p class="text-[10px] text-ink-400 mb-3">{{ strlen($tpl->message_text) }}/160 znakov | {{ $tpl->delay_minutes }} min</p>

                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard.sms-templates.edit', $tpl) }}" class="px-3 py-1.5 text-xs text-violet-600 border border-violet-200 dark:border-violet-800 rounded-lg hover:bg-violet-50 transition-fast">Upraviť</a>
                    <form method="POST" action="{{ route('dashboard.sms-templates.destroy', $tpl) }}" onsubmit="return confirm('Zmazať?')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 text-xs text-danger-600 border border-danger-200 rounded-lg hover:bg-danger-50 transition-fast">Zmazať</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif
@endsection
