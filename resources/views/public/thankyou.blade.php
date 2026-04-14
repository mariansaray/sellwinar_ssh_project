@extends('layouts.app')

@section('title', 'Ďakujeme — Sellwinar')

@section('body')
<div class="min-h-screen bg-white flex flex-col items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">

        {{-- Success icon --}}
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-success-50 flex items-center justify-center">
            <svg class="w-10 h-10 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="font-heading text-3xl font-bold text-ink-800 mb-3">
            {{ $config['headline'] ?? 'Ďakujeme za registráciu!' }}
        </h1>

        @if(!empty($config['message']))
            <p class="text-ink-600 mb-8">{{ $config['message'] }}</p>
        @else
            <p class="text-ink-600 mb-8">Pošleme vám pripomienku pred začiatkom webinára.</p>
        @endif

        {{-- Countdown --}}
        @if(!empty($config['show_countdown']) && $registrant->scheduled_at)
        <div class="mb-8" x-data="countdown('{{ $registrant->scheduled_at->toIso8601String() }}')" x-init="start()">
            <p class="text-sm text-ink-500 mb-3">Webinár začne za</p>
            <div class="flex justify-center gap-4">
                <div class="text-center">
                    <span class="font-heading text-4xl sm:text-5xl font-bold text-ink-800" x-text="days">0</span>
                    <p class="text-xs text-ink-400 mt-1">dní</p>
                </div>
                <span class="font-heading text-4xl sm:text-5xl font-bold text-ink-300">:</span>
                <div class="text-center">
                    <span class="font-heading text-4xl sm:text-5xl font-bold text-ink-800" x-text="hours">0</span>
                    <p class="text-xs text-ink-400 mt-1">hodín</p>
                </div>
                <span class="font-heading text-4xl sm:text-5xl font-bold text-ink-300">:</span>
                <div class="text-center">
                    <span class="font-heading text-4xl sm:text-5xl font-bold text-ink-800" x-text="minutes">0</span>
                    <p class="text-xs text-ink-400 mt-1">minút</p>
                </div>
                <span class="font-heading text-4xl sm:text-5xl font-bold text-ink-300">:</span>
                <div class="text-center">
                    <span class="font-heading text-4xl sm:text-5xl font-bold text-violet-600" x-text="seconds">0</span>
                    <p class="text-xs text-ink-400 mt-1">sekúnd</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Calendar buttons --}}
        @if(!empty($config['show_calendar_buttons']))
        <div class="flex flex-col sm:flex-row justify-center gap-3 mb-8">
            <a href="{{ $gcalUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-white border-2 border-ink-200 text-ink-700 text-sm font-semibold rounded-lg hover:bg-ink-50 transition-fast">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Pridať do Google Calendar
            </a>
        </div>
        @endif

        {{-- Interim CTA --}}
        @if(!empty($config['interim_cta_text']) && !empty($config['interim_cta_url']))
        <div class="mt-6 p-6 bg-violet-50 rounded-2xl">
            <p class="text-sm text-ink-600 mb-3">Kým čakáte:</p>
            <a href="{{ $config['interim_cta_url'] }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                {{ $config['interim_cta_text'] }}
            </a>
        </div>
        @endif

        {{-- Direct link to watch --}}
        <div class="mt-8 p-4 bg-ink-50 rounded-xl">
            <p class="text-xs text-ink-500 mb-2">Váš priamy odkaz na webinár:</p>
            <p class="text-sm font-mono text-violet-600 break-all">{{ route('public.watch', $registrant->access_token) }}</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('countdown', (targetDate) => ({
        days: '0', hours: '0', minutes: '0', seconds: '0',
        interval: null,
        start() {
            this.update();
            this.interval = setInterval(() => this.update(), 1000);
        },
        update() {
            const target = new Date(targetDate).getTime();
            const now = Date.now();
            const diff = Math.max(0, target - now);
            if (diff === 0 && this.interval) { clearInterval(this.interval); window.location.reload(); }
            this.days = String(Math.floor(diff / 86400000)).padStart(2, '0');
            this.hours = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
            this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
            this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        },
        destroy() { if (this.interval) clearInterval(this.interval); }
    }));
});
</script>
@endpush
@endsection
