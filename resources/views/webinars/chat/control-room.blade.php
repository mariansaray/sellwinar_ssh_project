@extends('layouts.dashboard')

@section('title', 'Kontrolná miestnosť — ' . $webinar->name)

@section('breadcrumbs')
    <a href="{{ route('dashboard.webinars.index') }}" class="hover:text-violet-600">Webináre</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <a href="{{ route('dashboard.webinars.edit', $webinar) }}" class="hover:text-violet-600">{{ $webinar->name }}</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Kontrolná miestnosť</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">Kontrolná miestnosť</h1>
            <p class="text-ink-500 dark:text-ink-400 mt-1">{{ $webinar->name }} — {{ $unreadCount }} neprečítaných správ</p>
        </div>
        <a href="{{ route('dashboard.webinars.edit', $webinar) }}?tab=chat" class="px-4 py-2 border border-ink-200 dark:border-ink-600 text-sm text-ink-600 dark:text-ink-400 rounded-lg hover:bg-ink-50 dark:hover:bg-ink-600 transition-fast">
            Späť na chat nastavenia
        </a>
    </div>

    @if($messages->isEmpty())
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-12 text-center">
            <i data-lucide="message-square" class="w-12 h-12 text-ink-300 dark:text-ink-500 mx-auto mb-4"></i>
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-2">Žiadne správy od divákov</h3>
            <p class="text-ink-500 dark:text-ink-400 text-sm">Keď diváci napíšu do chatu, ich správy sa zobrazia tu.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($messages as $msg)
            <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-4 {{ !$msg->is_read_by_admin ? 'border-l-4 border-l-violet-600' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="font-medium text-sm text-ink-800 dark:text-white">{{ $msg->sender_name }}</span>
                            @if($msg->registrant)
                                <span class="text-xs text-ink-400">{{ $msg->registrant->email }}</span>
                            @endif
                            <span class="text-xs text-ink-400">{{ $msg->created_at->format('d.m. H:i') }}</span>
                            @if(!$msg->is_read_by_admin)
                                <span class="inline-flex px-2 py-0.5 bg-violet-50 text-violet-700 text-[10px] font-semibold rounded-full">Nová</span>
                            @endif
                        </div>
                        <p class="text-sm text-ink-600 dark:text-ink-300">{{ $msg->message_text }}</p>
                    </div>
                </div>

                {{-- Reply form --}}
                <div x-data="{ showReply: false }" class="mt-3">
                    <button @click="showReply = !showReply" class="text-sm text-violet-600 hover:text-violet-500">
                        <i data-lucide="reply" class="w-3.5 h-3.5 inline mr-1"></i> Odpovedať
                    </button>
                    <div x-show="showReply" x-cloak class="mt-2">
                        <form method="POST" action="{{ route('dashboard.webinars.control-room.reply', $webinar) }}" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="registrant_id" value="{{ $msg->registrant_id }}">
                            <input type="text" name="message" required placeholder="Napíšte odpoveď..."
                                   class="flex-1 px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                            <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Odoslať</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $messages->links() }}</div>
    @endif
@endsection
