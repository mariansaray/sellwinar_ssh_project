@extends('layouts.dashboard')

@section('title', ($registrant->first_name ?? 'Divák') . ' — Sellwinar')

@section('breadcrumbs')
    <a href="{{ route('dashboard.registrants.index') }}" class="hover:text-violet-600">Diváci</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">{{ $registrant->first_name }} {{ $registrant->last_name }}</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="mb-6">
        <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $registrant->first_name }} {{ $registrant->last_name }}</h1>
        <p class="text-ink-500 dark:text-ink-400 mt-1">{{ $registrant->email }} | {{ $registrant->webinar->name ?? '—' }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info card --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Informácie</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-ink-400">Stav</dt><dd class="font-medium text-ink-800 dark:text-white">{{ $registrant->status }}</dd></div>
                <div><dt class="text-ink-400">Telefón</dt><dd class="text-ink-800 dark:text-white">{{ $registrant->phone ?? '—' }}</dd></div>
                <div><dt class="text-ink-400">Registrovaný</dt><dd class="text-ink-800 dark:text-white">{{ $registrant->created_at->format('d.m.Y H:i') }}</dd></div>
                <div><dt class="text-ink-400">Naplánovaný</dt><dd class="text-ink-800 dark:text-white">{{ $registrant->scheduled_at?->format('d.m.Y H:i') ?? '—' }}</dd></div>
                <div><dt class="text-ink-400">UTM Source</dt><dd class="text-ink-800 dark:text-white">{{ $registrant->utm_source ?? '—' }}</dd></div>
                <div><dt class="text-ink-400">UTM Medium</dt><dd class="text-ink-800 dark:text-white">{{ $registrant->utm_medium ?? '—' }}</dd></div>
                <div><dt class="text-ink-400">UTM Campaign</dt><dd class="text-ink-800 dark:text-white">{{ $registrant->utm_campaign ?? '—' }}</dd></div>
                <div><dt class="text-ink-400">IP</dt><dd class="text-ink-800 dark:text-white font-mono text-xs">{{ $registrant->registration_ip ?? '—' }}</dd></div>
            </dl>
        </div>

        {{-- Notifications --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Notifikácie</h3>
            @if($notifications->isEmpty())
                <p class="text-sm text-ink-400">Žiadne notifikácie.</p>
            @else
                <div class="space-y-2">
                    @foreach($notifications as $n)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $n->status === 'sent' ? 'bg-success-50 text-success-700' : ($n->status === 'failed' ? 'bg-danger-50 text-danger-700' : 'bg-ink-100 text-ink-500') }}">
                                {{ $n->status }}
                            </span>
                            <span class="text-ink-600 dark:text-ink-400">{{ $n->channel }}</span>
                        </div>
                        <span class="text-xs text-ink-400">{{ $n->created_at->format('d.m. H:i') }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Chat messages --}}
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Chat</h3>
            @if($chatMessages->isEmpty())
                <p class="text-sm text-ink-400">Žiadne chat správy.</p>
            @else
                <div class="space-y-2 max-h-[300px] overflow-y-auto">
                    @foreach($chatMessages as $msg)
                    <div class="text-sm {{ $msg->is_admin_reply ? 'pl-4 border-l-2 border-violet-600' : '' }}">
                        <span class="font-medium {{ $msg->is_admin_reply ? 'text-violet-600' : 'text-ink-800 dark:text-white' }}">{{ $msg->sender_name }}</span>
                        <span class="text-xs text-ink-400 ml-2">{{ $msg->created_at->format('H:i') }}</span>
                        <p class="text-ink-600 dark:text-ink-400">{{ $msg->message_text }}</p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Events timeline --}}
    <div class="mt-6 bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
        <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Aktivita</h3>
        @if($events->isEmpty())
            <p class="text-sm text-ink-400">Žiadna zaznamenaná aktivita.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-ink-50 dark:bg-ink-800">
                        <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Čas</th>
                        <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Udalosť</th>
                        <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Detail</th>
                    </tr></thead>
                    <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                        @foreach($events as $ev)
                        <tr>
                            <td class="px-3 py-2 text-xs text-ink-400 font-mono">{{ $ev->created_at->format('d.m. H:i:s') }}</td>
                            <td class="px-3 py-2 text-sm text-ink-800 dark:text-white">{{ $ev->event_type }}</td>
                            <td class="px-3 py-2 text-xs text-ink-500 font-mono">{{ json_encode($ev->event_data) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
