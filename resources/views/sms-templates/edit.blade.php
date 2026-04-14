@extends('layouts.dashboard')

@section('title', 'Upraviť SMS šablónu — Sellwinar')

@section('breadcrumbs')
    <a href="{{ route('dashboard.sms-templates.index') }}" class="hover:text-violet-600">SMS šablóny</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">{{ $smsTemplate->name }}</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="max-w-2xl">
        <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Upraviť SMS šablónu</h1>

        <form method="POST" action="{{ route('dashboard.sms-templates.update', $smsTemplate) }}" class="space-y-6">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Názov šablóny</label>
                <input type="text" name="name" value="{{ old('name', $smsTemplate->name) }}" required
                       class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Typ triggeru</label>
                    <select name="trigger_type" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                        @foreach($triggerTypes as $val => $label)
                            <option value="{{ $val }}" {{ $smsTemplate->trigger_type === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Oneskorenie (minúty)</label>
                    <input type="number" name="delay_minutes" value="{{ old('delay_minutes', $smsTemplate->delay_minutes) }}" required
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
            </div>

            <div x-data="{ text: `{{ addslashes(old('message_text', $smsTemplate->message_text)) }}` }">
                <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Text správy</label>
                <textarea name="message_text" rows="4" required maxlength="320" x-model="text"
                          class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">{{ old('message_text', $smsTemplate->message_text) }}</textarea>
                <p class="text-xs mt-1" :class="text.length > 160 ? 'text-warning-600' : 'text-ink-400'">
                    <span x-text="text.length">0</span>/160 znakov
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Uložiť zmeny
                </button>
                <a href="{{ route('dashboard.sms-templates.index') }}" class="px-6 py-2.5 text-sm text-ink-600 dark:text-ink-400 hover:text-ink-800 transition-fast">Zrušiť</a>
            </div>
        </form>
    </div>
@endsection
