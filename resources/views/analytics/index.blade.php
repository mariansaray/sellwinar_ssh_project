@extends('layouts.dashboard')

@section('title', 'Analytika — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">Analytika</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Analytika</h1>

    {{-- KPI --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        @foreach([
            ['label' => 'Dnes', 'value' => $stats['registrations_today'], 'icon' => 'user-plus', 'color' => 'success'],
            ['label' => 'Tento týždeň', 'value' => $stats['registrations_week'], 'icon' => 'trending-up', 'color' => 'info'],
            ['label' => 'Tento mesiac', 'value' => $stats['registrations_month'], 'icon' => 'calendar', 'color' => 'violet'],
            ['label' => 'Celkovo', 'value' => $stats['total_registrations'], 'icon' => 'users', 'color' => 'warning'],
            ['label' => 'CTA kliky', 'value' => $stats['total_cta_clicks'], 'icon' => 'mouse-pointer-click', 'color' => 'danger'],
            ['label' => 'Konverzia', 'value' => $stats['conversion_rate'] . '%', 'icon' => 'percent', 'color' => 'success'],
        ] as $kpi)
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-4 shadow-sm">
            <span class="text-[10px] font-semibold tracking-wide text-ink-500 uppercase">{{ $kpi['label'] }}</span>
            <p class="font-heading text-2xl font-bold text-ink-800 dark:text-white mt-1">{{ $kpi['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Chart --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Registrácie za 30 dní</h3>
            <canvas id="globalChart" height="200"></canvas>
        </div>

        {{-- Top webinars --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Top webináre</h3>
            @if($topWebinars->isEmpty())
                <p class="text-sm text-ink-400">Žiadne aktívne webináre.</p>
            @else
                <div class="space-y-3">
                    @foreach($topWebinars as $w)
                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard.webinars.analytics', $w) }}" class="text-sm font-medium text-ink-800 dark:text-white hover:text-violet-600">{{ $w->name }}</a>
                        <span class="text-sm font-semibold text-violet-600">{{ $w->registrants_count }} reg.</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($registrationsByDay);
    new Chart(document.getElementById('globalChart'), {
        type: 'line',
        data: {
            labels: Object.keys(data),
            datasets: [{ label: 'Registrácie', data: Object.values(data), borderColor: '#6C3AED', backgroundColor: 'rgba(108,58,237,0.1)', fill: true, tension: 0.3, pointRadius: 3, pointBackgroundColor: '#6C3AED' }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { ticks: { maxTicksLimit: 10 } } } }
    });
});
</script>
@endpush
