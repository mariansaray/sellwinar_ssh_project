@extends('layouts.dashboard')

@section('title', 'Nový webinár — Sellwinar')

@section('breadcrumbs')
    <a href="{{ route('dashboard.webinars.index') }}" class="hover:text-violet-600">Webináre</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">Nový webinár</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div class="max-w-2xl">
        <div class="mb-8">
            <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">Nový webinár</h1>
            <p class="text-ink-500 dark:text-ink-400 mt-1">Zadajte základné informácie o webinári</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-danger-50 dark:bg-danger-700/20 border-l-4 border-danger-500 rounded-lg p-4">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-danger-700 dark:text-danger-400">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('dashboard.webinars.store') }}" class="space-y-6">
            @csrf

            {{-- Type selection --}}
            <div>
                <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-3">Typ</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-data="{ type: '{{ old('type', request('type', 'evergreen')) }}' }">
                    <label @click="type = 'evergreen'"
                           :class="type === 'evergreen' ? 'border-violet-600 bg-violet-50 dark:bg-violet-900/20' : 'border-ink-200 dark:border-ink-600 bg-white dark:bg-ink-800'"
                           class="relative flex flex-col items-center p-6 border-2 rounded-xl cursor-pointer transition-fast hover:border-violet-400">
                        <input type="radio" name="type" value="evergreen" x-model="type" class="sr-only">
                        <i data-lucide="video" class="w-8 h-8 text-violet-600 mb-3"></i>
                        <span class="font-heading font-semibold text-ink-800 dark:text-white">Evergreen webinár</span>
                        <span class="text-xs text-ink-500 dark:text-ink-400 mt-1 text-center">Registrácia, pripomienky, chat, CTA</span>
                    </label>
                    <label @click="type = 'smart_video'"
                           :class="type === 'smart_video' ? 'border-violet-600 bg-violet-50 dark:bg-violet-900/20' : 'border-ink-200 dark:border-ink-600 bg-white dark:bg-ink-800'"
                           class="relative flex flex-col items-center p-6 border-2 rounded-xl cursor-pointer transition-fast hover:border-violet-400">
                        <input type="radio" name="type" value="smart_video" x-model="type" class="sr-only">
                        <i data-lucide="play-circle" class="w-8 h-8 text-info-600 mb-3"></i>
                        <span class="font-heading font-semibold text-ink-800 dark:text-white">Smart video</span>
                        <span class="text-xs text-ink-500 dark:text-ink-400 mt-1 text-center">Embed prehrávač s CTA na cudzej stránke</span>
                    </label>
                </div>
            </div>

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Názov webinára</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                       placeholder="Napr. Ako zdvojnásobiť tržby za 30 dní">
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">URL slug <span class="text-ink-400 font-normal">(voliteľné)</span></label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-ink-400">/w/</span>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                           class="flex-1 px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                           placeholder="auto-generovaný z názvu">
                </div>
            </div>

            {{-- Video source --}}
            <div>
                <label for="video_source" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Zdroj videa</label>
                <select id="video_source" name="video_source"
                        class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white focus:border-violet-600 ring-violet-focus transition-fast">
                    <option value="">Vybrať neskôr</option>
                    <option value="youtube" {{ old('video_source') === 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="vimeo" {{ old('video_source') === 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                    <option value="custom" {{ old('video_source') === 'custom' ? 'selected' : '' }}>Vlastná URL (MP4/HLS)</option>
                </select>
            </div>

            {{-- Video URL --}}
            <div>
                <label for="video_url" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">URL videa <span class="text-ink-400 font-normal">(voliteľné)</span></label>
                <input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}"
                       class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                       placeholder="https://www.youtube.com/watch?v=...">
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Vytvoriť webinár
                </button>
                <a href="{{ route('dashboard.webinars.index') }}" class="px-6 py-2.5 text-sm text-ink-600 dark:text-ink-400 hover:text-ink-800 dark:hover:text-white transition-fast">
                    Zrušiť
                </a>
            </div>
        </form>
    </div>
@endsection
