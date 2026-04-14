@extends('layouts.dashboard')

@section('title', 'Dashboard — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">Dashboard</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    {{-- Page title --}}
    <div class="mb-8">
        <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">Dashboard</h1>
        <p class="text-ink-500 dark:text-ink-400 mt-1">Prehľad vašich webinárov a metrík</p>
    </div>

    {{-- Trial banner --}}
    @if($tenant->isTrialing())
    <div class="mb-6 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i data-lucide="clock" class="w-5 h-5 text-violet-600"></i>
            <span class="text-sm text-ink-800 dark:text-violet-200">
                Váš trial končí za <strong>{{ now()->diffInDays($tenant->trial_ends_at) }} dní</strong>
            </span>
        </div>
        <a href="#" class="text-sm font-semibold text-violet-600 hover:text-violet-500">Vybrať plán</a>
    </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Active webinars --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm hover:shadow-md transition-fast">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium tracking-wide text-ink-500 dark:text-ink-400 uppercase">Aktívne webináre</span>
                <div class="w-9 h-9 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                    <i data-lucide="video" class="w-[18px] h-[18px] text-violet-600"></i>
                </div>
            </div>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $stats['active_webinars'] }}</p>
        </div>

        {{-- Active smart videos --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm hover:shadow-md transition-fast">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium tracking-wide text-ink-500 dark:text-ink-400 uppercase">Smart videá</span>
                <div class="w-9 h-9 rounded-lg bg-info-50 dark:bg-info-900/30 flex items-center justify-center">
                    <i data-lucide="play-circle" class="w-[18px] h-[18px] text-info-600"></i>
                </div>
            </div>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $stats['active_smart_videos'] }}</p>
        </div>

        {{-- Registrations today --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm hover:shadow-md transition-fast">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium tracking-wide text-ink-500 dark:text-ink-400 uppercase">Registrácie dnes</span>
                <div class="w-9 h-9 rounded-lg bg-success-50 dark:bg-success-900/30 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-[18px] h-[18px] text-success-600"></i>
                </div>
            </div>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $stats['registrations_today'] }}</p>
            <p class="text-xs text-ink-400 mt-1">Tento týždeň: {{ $stats['registrations_week'] }}</p>
        </div>

        {{-- Total registrations --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm hover:shadow-md transition-fast">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium tracking-wide text-ink-500 dark:text-ink-400 uppercase">Celkovo</span>
                <div class="w-9 h-9 rounded-lg bg-warning-50 dark:bg-warning-900/30 flex items-center justify-center">
                    <i data-lucide="users" class="w-[18px] h-[18px] text-warning-600"></i>
                </div>
            </div>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $stats['total_registrations'] }}</p>
            <p class="text-xs text-ink-400 mt-1">Tento mesiac: {{ $stats['registrations_month'] }}</p>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm">
        <h2 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Rýchle akcie</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('dashboard.webinars.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nový webinár
            </a>
            <a href="{{ route('dashboard.webinars.create') }}?type=smart_video" class="inline-flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-violet-600 text-violet-600 hover:bg-violet-50 dark:hover:bg-violet-900/20 text-sm font-semibold rounded-lg transition-fast">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nové smart video
            </a>
        </div>
    </div>
@endsection
