@extends('layouts.dashboard')

@section('title', 'Analytika — ' . $webinar->name)

@section('breadcrumbs')
    <a href="{{ route('dashboard.webinars.index') }}" class="hover:text-violet-600">Webináre</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <a href="{{ route('dashboard.webinars.edit', $webinar) }}" class="hover:text-violet-600">{{ $webinar->name }}</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Analytika</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">Analytika</h1>
        <a href="{{ route('dashboard.webinars.analytics.export', $webinar) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-ink-200 dark:border-ink-600 text-sm text-ink-600 dark:text-ink-400 rounded-lg hover:bg-ink-50 dark:hover:bg-ink-600 transition-fast">
            <i data-lucide="download" class="w-4 h-4"></i> Export CSV
        </a>
    </div>

    {{-- Funnel --}}
    <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 mb-6">
        <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Funnel</h3>
        <div class="flex items-end gap-2">
            @foreach($funnel as $i => $step)
            @php
                $maxCount = max(array_column($funnel, 'count')) ?: 1;
                $height = max(20, ($step['count'] / $maxCount) * 200);
                $prevCount = $i > 0 ? $funnel[$i-1]['count'] : null;
                $rate = $prevCount && $prevCount > 0 ? round(($step['count'] / $prevCount) * 100, 1) : null;
            @endphp
            <div class="flex-1 text-center">
                <div class="mx-auto w-full max-w-[120px]">
                    <div class="bg-violet-100 dark:bg-violet-900/30 rounded-t-lg mx-auto" style="height: {{ $height }}px; width: {{ 100 - ($i * 15) }}%"></div>
                </div>
                <p class="font-heading text-2xl font-bold text-ink-800 dark:text-white mt-2">{{ $step['count'] }}</p>
                <p class="text-xs text-ink-500 mt-1">{{ $step['label'] }}</p>
                @if($rate !== null)
                    <p class="text-xs text-violet-600 font-semibold">{{ $rate }}%</p>
                @endif
            </div>
            @if($i < count($funnel) - 1)
                <div class="pb-12 text-ink-300"><i data-lucide="chevron-right" class="w-5 h-5"></i></div>
            @endif
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Registrations chart --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Registrácie za 30 dní</h3>
            <canvas id="registrationsChart" height="200"></canvas>
        </div>

        {{-- UTM sources --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">UTM zdroje</h3>
            @if($utmSources->isEmpty())
                <p class="text-sm text-ink-400">Žiadne UTM dáta.</p>
            @else
                <div class="space-y-2">
                    @foreach($utmSources as $utm)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ink-700 dark:text-ink-300">{{ $utm->utm_source }}</span>
                        <span class="text-sm font-semibold text-ink-800 dark:text-white">{{ $utm->count }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Devices --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Zariadenia</h3>
            @if($devices->isEmpty())
                <p class="text-sm text-ink-400">Žiadne dáta.</p>
            @else
                <div class="space-y-2">
                    @foreach($devices as $d)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ink-700 dark:text-ink-300">{{ ucfirst($d->device_type) }}</span>
                        <span class="text-sm font-semibold text-ink-800 dark:text-white">{{ $d->count }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Average watch time --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Priemerná doba sledovania</h3>
            <p class="font-heading text-4xl font-bold text-violet-600">{{ gmdate('H:i:s', (int)$avgWatchTime) }}</p>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('registrationsChart');
    if (!ctx) return;

    const data = @json($registrationsByDay);
    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Registrácie',
                data: values,
                borderColor: '#6C3AED',
                backgroundColor: 'rgba(108, 58, 237, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointBackgroundColor: '#6C3AED',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { ticks: { maxTicksLimit: 10 } }
            }
        }
    });
});
</script>
@endpush
