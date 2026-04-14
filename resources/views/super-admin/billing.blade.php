@extends('layouts.dashboard')

@section('title', 'Billing prehľad — Super Admin')

@section('breadcrumbs')
    <span class="text-violet-600 font-medium">Super Admin</span>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Billing prehľad</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Billing prehľad</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm">
            <span class="text-xs font-medium tracking-wide text-ink-500 uppercase">MRR</span>
            <p class="font-heading text-3xl font-bold text-violet-600 mt-2">{{ number_format($mrr, 0, ',', ' ') }} &euro;</p>
        </div>
        @foreach($tenantsByPlan as $plan => $count)
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 shadow-sm">
            <span class="text-xs font-medium tracking-wide text-ink-500 uppercase">{{ ucfirst($plan) }}</span>
            <p class="font-heading text-3xl font-bold text-ink-800 dark:text-white mt-2">{{ $count }}</p>
            <p class="text-xs text-ink-400 mt-1">účtov</p>
        </div>
        @endforeach
    </div>

    <h2 class="font-heading text-xl font-semibold text-ink-800 dark:text-white mb-4">História platieb</h2>
    <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl overflow-hidden">
        @if($history->isEmpty())
            <div class="p-8 text-center text-sm text-ink-400">Žiadna história platieb.</div>
        @else
            <table class="w-full">
                <thead><tr class="bg-ink-50 dark:bg-ink-800">
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Dátum</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Tenant</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Suma</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Stav</th>
                </tr></thead>
                <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                    @foreach($history as $item)
                    <tr>
                        <td class="px-4 py-3 text-sm text-ink-500">{{ $item->created_at->format('d.m.Y') }}</td>
                        <td class="px-4 py-3 text-sm text-ink-800 dark:text-white">{{ $item->tenant->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-ink-800 dark:text-white">{{ number_format($item->amount, 2, ',', ' ') }} {{ $item->currency }}</td>
                        <td class="px-4 py-3"><span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $item->status === 'paid' ? 'bg-success-50 text-success-700' : 'bg-danger-50 text-danger-700' }}">{{ $item->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">{{ $history->links() }}</div>
        @endif
    </div>
@endsection
