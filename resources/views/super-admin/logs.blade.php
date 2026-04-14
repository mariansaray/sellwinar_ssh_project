@extends('layouts.dashboard')

@section('title', 'Systémové logy — Super Admin')

@section('breadcrumbs')
    <span class="text-violet-600 font-medium">Super Admin</span>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Systémové logy</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div x-data="{ tab: 'emails' }">
    <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Systémové logy</h1>

    <div class="border-b border-ink-200 dark:border-ink-600 mb-6">
        <nav class="flex gap-0">
            <button @click="tab = 'emails'" :class="tab === 'emails' ? 'border-violet-600 text-violet-600 font-semibold' : 'border-transparent text-ink-500'" class="px-4 py-3 text-sm border-b-2 transition-fast">E-mail logy</button>
            <button @click="tab = 'webhooks'" :class="tab === 'webhooks' ? 'border-violet-600 text-violet-600 font-semibold' : 'border-transparent text-ink-500'" class="px-4 py-3 text-sm border-b-2 transition-fast">Webhook logy</button>
        </nav>
    </div>

    <div x-show="tab === 'emails'" x-cloak>
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl overflow-hidden">
            @if($emailLogs->isEmpty())
                <div class="p-8 text-center text-sm text-ink-400">Žiadne e-mail logy.</div>
            @else
                <table class="w-full">
                    <thead><tr class="bg-ink-50 dark:bg-ink-800">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Čas</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Kanál</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Stav</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Chyba</th>
                    </tr></thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                        @foreach($emailLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-xs font-mono text-ink-500">{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            <td class="px-4 py-3 text-sm text-ink-800 dark:text-white">{{ $log->channel }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                                    {{ $log->status === 'sent' ? 'bg-success-50 text-success-700' : ($log->status === 'failed' ? 'bg-danger-50 text-danger-700' : 'bg-ink-100 text-ink-500') }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-danger-600 max-w-[300px] truncate">{{ $log->error_message ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div x-show="tab === 'webhooks'" x-cloak>
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl overflow-hidden">
            @if($webhookLogs->isEmpty())
                <div class="p-8 text-center text-sm text-ink-400">Žiadne webhook logy.</div>
            @else
                <table class="w-full">
                    <thead><tr class="bg-ink-50 dark:bg-ink-800">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Čas</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Event</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Stav</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">HTTP kód</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Pokus</th>
                    </tr></thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                        @foreach($webhookLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-xs font-mono text-ink-500">{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            <td class="px-4 py-3 text-sm text-ink-800 dark:text-white">{{ $log->event_type }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $log->status === 'success' ? 'bg-success-50 text-success-700' : 'bg-danger-50 text-danger-700' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-ink-500 font-mono">{{ $log->response_code ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-ink-500">{{ $log->attempt }}/3</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
