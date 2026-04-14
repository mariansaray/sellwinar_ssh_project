@extends('layouts.dashboard')

@section('title', 'SMS šablóny — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">SMS šablóny</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-2">SMS šablóny</h1>
    <p class="text-ink-500 dark:text-ink-400 mb-6">Prehľad SMS šablón. Najprv nastavte Twilio v <a href="{{ route('dashboard.settings.index') }}?tab=twilio" class="text-violet-600 hover:text-violet-500">Nastaveniach</a>.</p>

    @if($webinars->isEmpty())
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-12 text-center">
            <i data-lucide="smartphone" class="w-12 h-12 text-ink-300 mx-auto mb-4"></i>
            <p class="text-ink-500 text-sm">Najprv vytvorte webinár.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($webinars as $w)
            <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white">{{ $w->name }}</h3>
                    <span class="text-sm text-ink-400">{{ $w->sms_templates_count }} šablón</span>
                </div>
                @php $templates = $grouped[$w->id] ?? collect(); @endphp
                @if($templates->isEmpty())
                    <p class="text-sm text-ink-400">Žiadne SMS šablóny.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($templates as $tpl)
                        <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $tpl->is_active ? 'bg-success-50 text-success-700' : 'bg-ink-100 text-ink-400' }}">
                            {{ $tpl->trigger_type }}
                        </span>
                        @endforeach
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    @endif
@endsection
