@extends('layouts.dashboard')

@section('title', 'Nová SMS šablóna — Sellwinar')

@section('breadcrumbs')
    <a href="{{ route('dashboard.sms-templates.index') }}" class="hover:text-violet-600">SMS šablóny</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Nová šablóna</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="max-w-2xl">
        <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Nová SMS šablóna</h1>

        @if($errors->any())
            <div class="mb-6 bg-danger-50 dark:bg-danger-700/20 border-l-4 border-danger-500 rounded-lg p-4">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-danger-700 dark:text-danger-400">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('dashboard.sms-templates.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Názov šablóny</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Napr. Pripomienka 15 min — krátka"
                       class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Typ triggeru</label>
                    <select name="trigger_type" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                        @foreach($triggerTypes as $val => $label)
                            <option value="{{ $val }}" {{ old('trigger_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Oneskorenie (minúty)</label>
                    <input type="number" name="delay_minutes" value="{{ old('delay_minutes', -15) }}" required
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
            </div>

            <div x-data="{ text: '{{ old('message_text', '') }}' }">
                <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Text správy</label>
                <textarea name="message_text" rows="4" required maxlength="320" x-model="text"
                          class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast"
                          placeholder="@{{meno}}, webinár @{{nazov_webinara}} začne o 15 min! @{{link_na_webinar}}">{{ old('message_text') }}</textarea>
                <p class="text-xs mt-1" :class="text.length > 160 ? 'text-warning-600' : 'text-ink-400'">
                    <span x-text="text.length">0</span>/160 znakov
                    <span x-show="text.length > 160" class="text-warning-600">(bude rozdelená na viac SMS)</span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Vytvoriť šablónu
                </button>
                <a href="{{ route('dashboard.sms-templates.index') }}" class="px-6 py-2.5 text-sm text-ink-600 dark:text-ink-400 hover:text-ink-800 transition-fast">Zrušiť</a>
            </div>
        </form>
    </div>
@endsection
