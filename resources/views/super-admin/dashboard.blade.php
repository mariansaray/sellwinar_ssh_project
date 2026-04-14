@extends('layouts.dashboard')

@section('title', 'Super Admin — Sellwinar')

@section('breadcrumbs')
    <span class="text-violet-600 font-medium">Super Admin</span>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Dashboard</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Prehľad platformy</h1>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium tracking-wide text-ink-500 uppercase">Užívatelia</span>
                <div class="w-9 h-9 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                    <i data-lucide="users" class="w-[18px] h-[18px] text-violet-600"></i>
                </div>
            </div>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-ink-400 mt-1">Aktívne: {{ $stats['active_subscriptions'] }} | Trial: {{ $stats['trialing'] }}</p>
        </div>

        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium tracking-wide text-ink-500 uppercase">MRR</span>
                <div class="w-9 h-9 rounded-lg bg-success-50 dark:bg-success-900/30 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-[18px] h-[18px] text-success-600"></i>
                </div>
            </div>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ number_format($stats['mrr'], 0, ',', ' ') }} &euro;</p>
            <p class="text-xs text-ink-400 mt-1">Mesačný opakovaný príjem</p>
        </div>

        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium tracking-wide text-ink-500 uppercase">Webináre</span>
                <div class="w-9 h-9 rounded-lg bg-info-50 dark:bg-info-900/30 flex items-center justify-center">
                    <i data-lucide="video" class="w-[18px] h-[18px] text-info-600"></i>
                </div>
            </div>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $stats['total_webinars'] }}</p>
        </div>

        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium tracking-wide text-ink-500 uppercase">Registrácie</span>
                <div class="w-9 h-9 rounded-lg bg-warning-50 dark:bg-warning-900/30 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-[18px] h-[18px] text-warning-600"></i>
                </div>
            </div>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $stats['total_registrants'] }}</p>
        </div>
    </div>

    {{-- New users chart --}}
    <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
        <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Noví užívatelia (30 dní)</h3>
        <canvas id="newUsersChart" height="200"></canvas>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($newUsersByDay);
    new Chart(document.getElementById('newUsersChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(data),
            datasets: [{ label: 'Noví užívatelia', data: Object.values(data), backgroundColor: 'rgba(108,58,237,0.6)', borderRadius: 6 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { ticks: { maxTicksLimit: 15 } } } }
    });
});
</script>
@endpush
