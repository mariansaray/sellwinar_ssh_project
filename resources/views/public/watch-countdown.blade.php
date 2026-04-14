@extends('layouts.app')

@section('title', $webinar->name)

@section('body')
<div class="min-h-screen bg-ink-900 flex items-center justify-center px-4" x-data="countdown('{{ $registrant->scheduled_at->toIso8601String() }}')" x-init="start()">
    <div class="text-center">
        <h1 class="font-heading text-3xl font-bold text-white mb-4">{{ $webinar->name }}</h1>
        <p class="text-ink-400 mb-8">Webinár začne o</p>
        <div class="flex justify-center gap-4 mb-8">
            <div class="text-center"><span class="font-heading text-5xl font-bold text-white" x-text="hours">0</span><p class="text-xs text-ink-500 mt-1">hodín</p></div>
            <span class="font-heading text-5xl font-bold text-ink-600">:</span>
            <div class="text-center"><span class="font-heading text-5xl font-bold text-white" x-text="minutes">0</span><p class="text-xs text-ink-500 mt-1">minút</p></div>
            <span class="font-heading text-5xl font-bold text-ink-600">:</span>
            <div class="text-center"><span class="font-heading text-5xl font-bold text-violet-500" x-text="seconds">0</span><p class="text-xs text-ink-500 mt-1">sekúnd</p></div>
        </div>
        <p class="text-sm text-ink-500">Táto stránka sa automaticky aktualizuje keď webinár začne.</p>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('countdown', (targetDate) => ({
        days: '0', hours: '0', minutes: '0', seconds: '0', interval: null,
        start() { this.update(); this.interval = setInterval(() => this.update(), 1000); },
        update() {
            const diff = Math.max(0, new Date(targetDate).getTime() - Date.now());
            if (diff === 0) { clearInterval(this.interval); window.location.reload(); return; }
            this.hours = String(Math.floor(diff / 3600000)).padStart(2, '0');
            this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
            this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        }
    }));
});
</script>
@endpush
@endsection
