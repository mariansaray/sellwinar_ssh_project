@extends('layouts.dashboard')

@section('title', 'Billing — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">Billing</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Billing</h1>

    {{-- Current plan --}}
    <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-ink-500 dark:text-ink-400">Aktuálny plán</p>
                <p class="font-heading text-2xl font-bold text-ink-800 dark:text-white mt-1">{{ $currentPlan->name ?? ucfirst($tenant->plan) }}</p>
                @if($tenant->isTrialing())
                    <p class="text-sm text-violet-600 mt-1">Trial končí {{ $tenant->trial_ends_at->format('d.m.Y') }} (zostáva {{ now()->diffInDays($tenant->trial_ends_at) }} dní)</p>
                @endif
            </div>
            <div>
                <span class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-full {{ $tenant->isActive() ? 'bg-success-50 text-success-700' : 'bg-danger-50 text-danger-700' }}">
                    {{ $tenant->isActive() ? 'Aktívny' : 'Neaktívny' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Plans --}}
    <h2 class="font-heading text-xl font-semibold text-ink-800 dark:text-white mb-4">Dostupné plány</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        @foreach($plans as $plan)
        <div class="bg-white dark:bg-ink-700 border-2 rounded-xl p-6 {{ $tenant->plan === $plan->slug ? 'border-violet-600' : 'border-ink-200 dark:border-ink-600' }}">
            @if($tenant->plan === $plan->slug)
                <span class="inline-flex px-2.5 py-1 bg-violet-50 dark:bg-violet-900/20 text-violet-700 text-xs font-semibold rounded-full mb-3">Aktuálny</span>
            @endif
            <h3 class="font-heading text-xl font-bold text-ink-800 dark:text-white">{{ $plan->name }}</h3>
            <div class="mt-2 mb-4">
                <span class="font-heading text-3xl font-bold text-violet-600">{{ number_format($plan->price, 0, ',', ' ') }} &euro;</span>
                <span class="text-sm text-ink-400">/ {{ $plan->interval === 'monthly' ? 'mesiac' : ($plan->interval === 'yearly' ? 'rok' : 'jednorazovo') }}</span>
            </div>
            @if($plan->features)
                <ul class="space-y-2 mb-6">
                    @foreach($plan->features as $feature)
                    <li class="flex items-start gap-2 text-sm text-ink-600 dark:text-ink-400">
                        <svg class="w-4 h-4 text-success-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
            @endif
            @if($tenant->plan !== $plan->slug)
                <button class="w-full py-2.5 border-2 border-violet-600 text-violet-600 font-semibold text-sm rounded-lg hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-fast">
                    Vybrať plán
                </button>
                <p class="text-[10px] text-ink-400 mt-2 text-center">Stripe integrácia bude dostupná čoskoro</p>
            @endif
        </div>
        @endforeach
    </div>

    {{-- History --}}
    <h2 class="font-heading text-xl font-semibold text-ink-800 dark:text-white mb-4">História platieb</h2>
    <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl overflow-hidden">
        @if($history->isEmpty())
            <div class="p-8 text-center text-sm text-ink-400">Žiadna história platieb.</div>
        @else
            <table class="w-full">
                <thead><tr class="bg-ink-50 dark:bg-ink-800">
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Dátum</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Suma</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Stav</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Obdobie</th>
                </tr></thead>
                <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                    @foreach($history as $item)
                    <tr>
                        <td class="px-4 py-3 text-sm text-ink-800 dark:text-white">{{ $item->created_at->format('d.m.Y') }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-ink-800 dark:text-white">{{ number_format($item->amount, 2, ',', ' ') }} {{ $item->currency }}</td>
                        <td class="px-4 py-3"><span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $item->status === 'paid' ? 'bg-success-50 text-success-700' : 'bg-danger-50 text-danger-700' }}">{{ $item->status }}</span></td>
                        <td class="px-4 py-3 text-sm text-ink-500">{{ $item->period_start?->format('d.m.Y') }} — {{ $item->period_end?->format('d.m.Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
