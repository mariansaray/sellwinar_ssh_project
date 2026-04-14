@extends('layouts.dashboard')

@section('title', $webinar->name . ' — Sellwinar')

@section('breadcrumbs')
    <a href="{{ route('dashboard.webinars.index') }}" class="hover:text-violet-600">Webináre</a>
    <i data-lucide="chevron-right" class="w-4 h-4 text-ink-300"></i>
    <span class="text-ink-800 dark:text-white font-medium">{{ $webinar->name }}</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
    <div x-data="{ tab: '{{ request('tab', 'info') }}' }">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white">{{ $webinar->name }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    @switch($webinar->status)
                        @case('active')
                            <span class="inline-flex px-2.5 py-1 bg-success-50 text-success-700 text-xs font-medium rounded-full">Aktívny</span>
                            @break
                        @case('paused')
                            <span class="inline-flex px-2.5 py-1 bg-warning-50 text-warning-700 text-xs font-medium rounded-full">Pozastavený</span>
                            @break
                        @case('draft')
                            <span class="inline-flex px-2.5 py-1 bg-ink-100 text-ink-600 text-xs font-medium rounded-full">Koncept</span>
                            @break
                        @case('archived')
                            <span class="inline-flex px-2.5 py-1 bg-ink-100 text-ink-400 text-xs font-medium rounded-full">Archivovaný</span>
                            @break
                    @endswitch
                    <span class="text-sm text-ink-400">/{{ $webinar->slug }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('dashboard.webinars.toggle-status', $webinar) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-ink-200 dark:border-ink-600 text-sm text-ink-600 dark:text-ink-400 rounded-lg hover:bg-ink-50 dark:hover:bg-ink-600 transition-fast">
                        {{ $webinar->status === 'active' ? 'Pozastaviť' : 'Aktivovať' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Tab navigation --}}
        <div class="border-b border-ink-200 dark:border-ink-600 mb-6 overflow-x-auto">
            <nav class="flex gap-0 min-w-max">
                @php
                    $tabs = [
                        'info' => 'Info',
                        'video' => 'Video',
                        'player' => 'Player',
                    ];
                    if ($webinar->isEvergreen()) {
                        $tabs += [
                            'schedule' => 'Plánovanie',
                            'registration' => 'Reg. stránka',
                            'thankyou' => 'Thank You',
                            'chat' => 'Chat',
                        ];
                    }
                    $tabs += [
                        'cta' => 'CTA',
                        'alerts' => 'Alerty',
                        'emails' => 'E-maily',
                        'tracking' => 'Tracking',
                        'embed' => 'Embed kód',
                        'analytics' => 'Analytika',
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-violet-600 text-violet-600 font-semibold' : 'border-transparent text-ink-500 dark:text-ink-400 hover:text-ink-700 dark:hover:text-ink-200'"
                            class="px-4 py-3 text-sm border-b-2 transition-fast whitespace-nowrap">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab: Info --}}
        <div x-show="tab === 'info'" x-cloak>
            <form method="POST" action="{{ route('dashboard.webinars.update', $webinar) }}" class="max-w-2xl space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="tab" value="info">

                <div>
                    <label for="name" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Názov</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $webinar->name) }}" required
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">URL slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $webinar->slug) }}" required
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Stav</label>
                    <select id="status" name="status"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                        <option value="draft" {{ $webinar->status === 'draft' ? 'selected' : '' }}>Koncept</option>
                        <option value="active" {{ $webinar->status === 'active' ? 'selected' : '' }}>Aktívny</option>
                        <option value="paused" {{ $webinar->status === 'paused' ? 'selected' : '' }}>Pozastavený</option>
                        <option value="archived" {{ $webinar->status === 'archived' ? 'selected' : '' }}>Archivovaný</option>
                    </select>
                </div>

                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Uložiť zmeny
                </button>
            </form>
        </div>

        {{-- Tab: Video --}}
        <div x-show="tab === 'video'" x-cloak>
            <form method="POST" action="{{ route('dashboard.webinars.update', $webinar) }}" class="max-w-2xl space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="tab" value="video">

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Zdroj videa</label>
                    <select name="video_source"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                        <option value="youtube" {{ $webinar->video_source === 'youtube' ? 'selected' : '' }}>YouTube</option>
                        <option value="vimeo" {{ $webinar->video_source === 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                        <option value="custom" {{ $webinar->video_source === 'custom' ? 'selected' : '' }}>Vlastná URL</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">URL videa</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $webinar->video_url) }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast"
                           placeholder="https://www.youtube.com/watch?v=...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Dĺžka videa (sekundy)</label>
                    <input type="number" name="video_duration_seconds" value="{{ old('video_duration_seconds', $webinar->video_duration_seconds) }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast"
                           placeholder="3600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Náhľadový obrázok (URL)</label>
                    <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url', $webinar->thumbnail_url) }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast"
                           placeholder="https://...">
                </div>

                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Uložiť zmeny
                </button>
            </form>
        </div>

        {{-- Tab: Player --}}
        <div x-show="tab === 'player'" x-cloak>
            @php $pc = $webinar->player_config ?? []; @endphp
            <form method="POST" action="{{ route('dashboard.webinars.update', $webinar) }}" class="max-w-2xl space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="tab" value="player">

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Primárna farba</label>
                    <input type="color" name="primaryColor" value="{{ $pc['primaryColor'] ?? '#6C3AED' }}"
                           class="w-16 h-10 border border-ink-200 dark:border-ink-600 rounded-md cursor-pointer">
                </div>

                <div class="space-y-3">
                    <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white">Ovládacie prvky</h3>

                    @foreach([
                        'showPlayPause' => 'Zobraziť Play/Pause',
                        'showProgress' => 'Zobraziť progress bar',
                        'showVolume' => 'Zobraziť hlasitosť',
                        'showFullscreen' => 'Zobraziť fullscreen',
                        'showSpeed' => 'Zobraziť rýchlosť prehrávania',
                    ] as $key => $label)
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input type="checkbox" name="{{ $key }}" value="1" {{ ($pc[$key] ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="space-y-3">
                    <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white">Správanie</h3>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="autoplay" value="0">
                        <input type="checkbox" name="autoplay" value="1" {{ ($pc['autoplay'] ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Automatické prehranie (muted)</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="startMuted" value="0">
                        <input type="checkbox" name="startMuted" value="1" {{ ($pc['startMuted'] ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Začať stlmený</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="allowSeeking" value="0">
                        <input type="checkbox" name="allowSeeking" value="1" {{ ($pc['allowSeeking'] ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Povoliť pretáčanie</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="fakeProgressBar" value="0">
                        <input type="checkbox" name="fakeProgressBar" value="1" {{ ($pc['fakeProgressBar'] ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Fake progress bar</span>
                    </label>

                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Fake dĺžka videa (sekundy)</label>
                        <input type="number" name="fakeDurationSeconds" value="{{ $pc['fakeDurationSeconds'] ?? '' }}"
                               class="w-48 px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast"
                               placeholder="Napr. 1800">
                        <p class="text-xs text-ink-400 mt-1">Ak je zapnutý fake progress bar, toto je zobrazená dĺžka</p>
                    </div>
                </div>

                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Uložiť zmeny
                </button>
            </form>
        </div>

        {{-- Tab: Schedule (Evergreen only) --}}
        @if($webinar->isEvergreen())
        <div x-show="tab === 'schedule'" x-cloak>
            @php $sched = $webinar->schedule; @endphp
            <form method="POST" action="{{ route('dashboard.webinars.update', $webinar) }}" class="max-w-2xl space-y-6" x-data="{ scheduleType: '{{ $sched->schedule_type ?? 'jit' }}' }">
                @csrf @method('PUT')
                <input type="hidden" name="tab" value="schedule">

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-3">Typ plánovania</label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-fast"
                               :class="scheduleType === 'jit' ? 'border-violet-600 bg-violet-50 dark:bg-violet-900/20' : 'border-ink-200 dark:border-ink-600'">
                            <input type="radio" name="schedule_type" value="jit" x-model="scheduleType" class="mt-0.5">
                            <div>
                                <span class="text-sm font-semibold text-ink-800 dark:text-white">Just-in-time</span>
                                <p class="text-xs text-ink-500 mt-1">Webinár začne X minút po registrácii. Najvyšší konverzný pomer.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-fast"
                               :class="scheduleType === 'fixed' ? 'border-violet-600 bg-violet-50 dark:bg-violet-900/20' : 'border-ink-200 dark:border-ink-600'">
                            <input type="radio" name="schedule_type" value="fixed" x-model="scheduleType" class="mt-0.5">
                            <div>
                                <span class="text-sm font-semibold text-ink-800 dark:text-white">Fixné časy</span>
                                <p class="text-xs text-ink-500 mt-1">Opakujúce sa časy — napr. každý utorok a štvrtok o 19:00.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-fast"
                               :class="scheduleType === 'interval' ? 'border-violet-600 bg-violet-50 dark:bg-violet-900/20' : 'border-ink-200 dark:border-ink-600'">
                            <input type="radio" name="schedule_type" value="interval" x-model="scheduleType" class="mt-0.5">
                            <div>
                                <span class="text-sm font-semibold text-ink-800 dark:text-white">Interval</span>
                                <p class="text-xs text-ink-500 mt-1">Webinár každých X hodín. Divák vidí najbližší čas.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="scheduleType === 'jit'">
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Oneskorenie (minúty po registrácii)</label>
                    <input type="number" name="jit_delay_minutes" value="{{ $sched->jit_delay_minutes ?? 15 }}" min="1"
                           class="w-48 px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div x-show="scheduleType === 'interval'">
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Interval (hodiny)</label>
                    <input type="number" name="interval_hours" value="{{ $sched->interval_hours ?? 2 }}" min="1"
                           class="w-48 px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Časová zóna</label>
                    <select name="timezone" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                        <option value="Europe/Bratislava" {{ ($sched->timezone ?? '') === 'Europe/Bratislava' ? 'selected' : '' }}>Europe/Bratislava (CET)</option>
                        <option value="Europe/Prague" {{ ($sched->timezone ?? '') === 'Europe/Prague' ? 'selected' : '' }}>Europe/Prague (CET)</option>
                        <option value="UTC" {{ ($sched->timezone ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                    </select>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="hide_night_times" value="0">
                    <input type="checkbox" name="hide_night_times" value="1" {{ ($sched->hide_night_times ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                    <span class="text-sm text-ink-700 dark:text-ink-300">Skryť nočné termíny (23:00 - 7:00)</span>
                </label>

                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Uložiť zmeny
                </button>
            </form>
        </div>

        {{-- Tab: Registration page --}}
        <div x-show="tab === 'registration'" x-cloak>
            @php $rpc = $webinar->registration_page_config ?? []; @endphp
            <form method="POST" action="{{ route('dashboard.webinars.update', $webinar) }}" class="max-w-2xl space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="tab" value="registration">

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-3">Šablóna</label>
                    <div class="grid grid-cols-5 gap-2">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="flex flex-col items-center p-3 border rounded-xl cursor-pointer transition-fast hover:border-violet-400">
                            <input type="radio" name="template" value="{{ $i }}" {{ ($rpc['template'] ?? '1') == $i ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-full h-16 bg-ink-100 dark:bg-ink-600 rounded-lg mb-2 peer-checked:ring-2 peer-checked:ring-violet-600"></div>
                            <span class="text-xs text-ink-500">Šablóna {{ $i }}</span>
                        </label>
                        @endfor
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Nadpis</label>
                    <input type="text" name="headline" value="{{ $rpc['headline'] ?? $webinar->name }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Podnadpis</label>
                    <input type="text" name="subheadline" value="{{ $rpc['subheadline'] ?? '' }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Popis / Benefits</label>
                    <textarea name="description" rows="4"
                              class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">{{ $rpc['description'] ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Text CTA tlačidla</label>
                    <input type="text" name="cta_text" value="{{ $rpc['cta_text'] ?? 'Registrovať sa zadarmo' }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Primárna farba</label>
                        <input type="color" name="primary_color" value="{{ $rpc['primary_color'] ?? '#6C3AED' }}"
                               class="w-16 h-10 border border-ink-200 dark:border-ink-600 rounded-md cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Farba pozadia</label>
                        <input type="color" name="background_color" value="{{ $rpc['background_color'] ?? '#FFFFFF' }}"
                               class="w-16 h-10 border border-ink-200 dark:border-ink-600 rounded-md cursor-pointer">
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="show_social_proof" value="0">
                        <input type="checkbox" name="show_social_proof" value="1" {{ ($rpc['show_social_proof'] ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Zobraziť social proof ("X ľudí sa registrovalo")</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="show_countdown" value="0">
                        <input type="checkbox" name="show_countdown" value="1" {{ ($rpc['show_countdown'] ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Zobraziť countdown</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="require_phone" value="0">
                        <input type="checkbox" name="require_phone" value="1" {{ ($rpc['require_phone'] ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Vyžadovať telefónne číslo</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Custom CSS <span class="text-ink-400 font-normal">(pre pokročilých)</span></label>
                    <textarea name="custom_css" rows="3"
                              class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm font-mono focus:border-violet-600 ring-violet-focus transition-fast"
                              placeholder=".registration-form { }">{{ $rpc['custom_css'] ?? '' }}</textarea>
                </div>

                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Uložiť zmeny
                </button>
            </form>
        </div>

        {{-- Tab: Thank You --}}
        <div x-show="tab === 'thankyou'" x-cloak>
            @php $tyc = $webinar->thankyou_page_config ?? []; @endphp
            <form method="POST" action="{{ route('dashboard.webinars.update', $webinar) }}" class="max-w-2xl space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="tab" value="thankyou">

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Nadpis</label>
                    <input type="text" name="headline" value="{{ $tyc['headline'] ?? 'Ďakujeme za registráciu!' }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Správa</label>
                    <textarea name="message" rows="3"
                              class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">{{ $tyc['message'] ?? '' }}</textarea>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="show_countdown" value="0">
                        <input type="checkbox" name="show_countdown" value="1" {{ ($tyc['show_countdown'] ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Zobraziť odpočítavanie</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="show_calendar_buttons" value="0">
                        <input type="checkbox" name="show_calendar_buttons" value="1" {{ ($tyc['show_calendar_buttons'] ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Zobraziť "Pridať do kalendára"</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Medzičas CTA text <span class="text-ink-400 font-normal">(voliteľné)</span></label>
                    <input type="text" name="interim_cta_text" value="{{ $tyc['interim_cta_text'] ?? '' }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast"
                           placeholder="Pozri si toto video kým čakáš">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Medzičas CTA URL</label>
                    <input type="url" name="interim_cta_url" value="{{ $tyc['interim_cta_url'] ?? '' }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Uložiť zmeny
                </button>
            </form>
        </div>

        {{-- Tab: Chat --}}
        <div x-show="tab === 'chat'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Config --}}
                <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                    <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Nastavenia chatu</h3>
                    @php $cc = $webinar->chatConfig; @endphp
                    <form method="POST" action="{{ route('dashboard.webinars.chat.config', $webinar) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Diváci (min)</label>
                                <input type="number" name="viewer_count_min" value="{{ $cc->viewer_count_min ?? 45 }}" min="0"
                                       class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Diváci (max)</label>
                                <input type="number" name="viewer_count_max" value="{{ $cc->viewer_count_max ?? 120 }}" min="1"
                                       class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                            </div>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Uložiť</button>
                    </form>
                </div>

                {{-- Control room link --}}
                <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                    <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Kontrolná miestnosť</h3>
                    <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">Sledujte správy od divákov v reálnom čase a odpovedajte im.</p>
                    <a href="{{ route('dashboard.webinars.control-room', $webinar) }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">
                        <i data-lucide="message-square" class="w-4 h-4"></i> Otvoriť kontrolnú miestnosť
                    </a>
                </div>
            </div>

            {{-- Add fake message --}}
            <div class="mt-6 bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Fejkové správy</h3>
                <form method="POST" action="{{ route('dashboard.webinars.chat.store', $webinar) }}" class="flex flex-wrap items-end gap-3 mb-4">
                    @csrf
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs text-ink-500 mb-1">Meno</label>
                        <input type="text" name="sender_name" required placeholder="Andrea K." class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <div class="flex-[2] min-w-[200px]">
                        <label class="block text-xs text-ink-500 mb-1">Správa</label>
                        <input type="text" name="message_text" required placeholder="Super, toto som presne potrebovala!" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <div class="w-24">
                        <label class="block text-xs text-ink-500 mb-1">Čas (s)</label>
                        <input type="number" name="display_at_seconds" required min="0" value="60" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <input type="hidden" name="message_type" value="message">
                    <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Pridať</button>
                </form>

                {{-- CSV Import --}}
                <details class="mb-4">
                    <summary class="text-sm text-violet-600 cursor-pointer hover:text-violet-500">Import z CSV</summary>
                    <form method="POST" action="{{ route('dashboard.webinars.chat.import', $webinar) }}" class="mt-3">
                        @csrf
                        <textarea name="csv_data" rows="4" placeholder="meno,správa,sekundy,typ&#10;Andrea K.,Super webinár!,60,message&#10;Peter M.,Mám otázku...,120,question" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm font-mono focus:border-violet-600 ring-violet-focus transition-fast"></textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-ink-600 hover:bg-ink-500 text-white text-sm rounded-lg transition-fast">Importovať</button>
                    </form>
                </details>

                {{-- Messages list --}}
                @php $fakeMessages = $webinar->chatMessagesFake()->orderBy('display_at_seconds')->get(); @endphp
                @if($fakeMessages->isEmpty())
                    <p class="text-sm text-ink-400 py-4">Zatiaľ žiadne fejkové správy.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead><tr class="bg-ink-50 dark:bg-ink-800">
                                <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Čas</th>
                                <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Meno</th>
                                <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Správa</th>
                                <th class="px-3 py-2"></th>
                            </tr></thead>
                            <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                                @foreach($fakeMessages as $msg)
                                <tr class="hover:bg-violet-50 dark:hover:bg-ink-600 transition-fast">
                                    <td class="px-3 py-2 text-sm font-mono text-ink-500">{{ gmdate('H:i:s', $msg->display_at_seconds) }}</td>
                                    <td class="px-3 py-2 text-sm font-medium text-ink-800 dark:text-white">{{ $msg->sender_name }}</td>
                                    <td class="px-3 py-2 text-sm text-ink-600 dark:text-ink-400">{{ Str::limit($msg->message_text, 60) }}</td>
                                    <td class="px-3 py-2">
                                        <form method="POST" action="{{ route('dashboard.webinars.chat.destroy', [$webinar, $msg]) }}" onsubmit="return confirm('Zmazať?')">
                                            @csrf @method('DELETE')
                                            <button class="p-1 text-ink-400 hover:text-danger-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Tab: CTA --}}
        <div x-show="tab === 'cta'" x-cloak>
            @php $cta = $webinar->cta_config ?? []; @endphp
            <form method="POST" action="{{ route('dashboard.webinars.update', $webinar) }}" class="max-w-2xl space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="tab" value="cta">

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Text tlačidla</label>
                    <input type="text" name="text" value="{{ $cta['text'] ?? 'Chcem sa prihlásiť' }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">URL (kam smeruje)</label>
                    <input type="url" name="url" value="{{ $cta['url'] ?? '' }}"
                           class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast"
                           placeholder="https://...">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Zobraziť v čase (sekundy)</label>
                        <input type="number" name="show_at_seconds" value="{{ $cta['show_at_seconds'] ?? 1800 }}" min="0"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Skryť v čase <span class="text-ink-400 font-normal">(voliteľné)</span></label>
                        <input type="number" name="hide_at_seconds" value="{{ $cta['hide_at_seconds'] ?? '' }}" min="0"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast"
                               placeholder="Natrvalo">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Farba tlačidla</label>
                        <input type="color" name="button_color" value="{{ $cta['button_color'] ?? '#6C3AED' }}"
                               class="w-16 h-10 border border-ink-200 dark:border-ink-600 rounded-md cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Farba textu</label>
                        <input type="color" name="text_color" value="{{ $cta['text_color'] ?? '#FFFFFF' }}"
                               class="w-16 h-10 border border-ink-200 dark:border-ink-600 rounded-md cursor-pointer">
                    </div>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="sticky_on_mobile" value="0">
                    <input type="checkbox" name="sticky_on_mobile" value="1" {{ ($cta['sticky_on_mobile'] ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                    <span class="text-sm text-ink-700 dark:text-ink-300">Sticky na mobile (prilepené na spodku)</span>
                </label>

                <button type="submit" class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
                    Uložiť zmeny
                </button>
            </form>
        </div>

        {{-- Tab: Alerts --}}
        <div x-show="tab === 'alerts'" x-cloak>
            <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Purchase alerty</h3>
                <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">Fejkové notifikácie o nákupoch, synchronizované s časom videa.</p>

                <form method="POST" action="{{ route('dashboard.webinars.alerts.store', $webinar) }}" class="flex flex-wrap items-end gap-3 mb-4">
                    @csrf
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs text-ink-500 mb-1">Meno kupujúceho</label>
                        <input type="text" name="buyer_name" required placeholder="Andrea K." class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs text-ink-500 mb-1">Produkt</label>
                        <input type="text" name="product_name" required placeholder="Premium balík" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <div class="w-24">
                        <label class="block text-xs text-ink-500 mb-1">Čas (s)</label>
                        <input type="number" name="display_at_seconds" required min="0" value="1850" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Pridať</button>
                </form>

                <details class="mb-4">
                    <summary class="text-sm text-violet-600 cursor-pointer hover:text-violet-500">Import z CSV</summary>
                    <form method="POST" action="{{ route('dashboard.webinars.alerts.import', $webinar) }}" class="mt-3">
                        @csrf
                        <textarea name="csv_data" rows="3" placeholder="meno,produkt,sekundy&#10;Andrea K.,Premium balík,1850&#10;Peter M.,Kurz XYZ,2100" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm font-mono focus:border-violet-600 ring-violet-focus transition-fast"></textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-ink-600 hover:bg-ink-500 text-white text-sm rounded-lg transition-fast">Importovať</button>
                    </form>
                </details>

                @php $alerts = $webinar->purchaseAlerts()->orderBy('display_at_seconds')->get(); @endphp
                @if($alerts->isEmpty())
                    <p class="text-sm text-ink-400 py-4">Zatiaľ žiadne alerty.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead><tr class="bg-ink-50 dark:bg-ink-800">
                                <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Čas</th>
                                <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Meno</th>
                                <th class="text-left px-3 py-2 text-xs font-semibold uppercase text-ink-500">Produkt</th>
                                <th class="px-3 py-2"></th>
                            </tr></thead>
                            <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                                @foreach($alerts as $alert)
                                <tr class="hover:bg-violet-50 dark:hover:bg-ink-600 transition-fast">
                                    <td class="px-3 py-2 text-sm font-mono text-ink-500">{{ gmdate('H:i:s', $alert->display_at_seconds) }}</td>
                                    <td class="px-3 py-2 text-sm text-ink-800 dark:text-white">{{ $alert->buyer_name }}</td>
                                    <td class="px-3 py-2 text-sm text-ink-600 dark:text-ink-400">{{ $alert->product_name }}</td>
                                    <td class="px-3 py-2">
                                        <form method="POST" action="{{ route('dashboard.webinars.alerts.destroy', [$webinar, $alert]) }}" onsubmit="return confirm('Zmazať?')">
                                            @csrf @method('DELETE')
                                            <button class="p-1 text-ink-400 hover:text-danger-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab: Emails --}}
        <div x-show="tab === 'emails'" x-cloak>
            <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white">E-mail šablóny</h3>
                </div>
                <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">Placeholdery: <code class="text-xs bg-ink-100 dark:bg-ink-800 px-1.5 py-0.5 rounded">@{{meno}}</code> <code class="text-xs bg-ink-100 dark:bg-ink-800 px-1.5 py-0.5 rounded">@{{email}}</code> <code class="text-xs bg-ink-100 dark:bg-ink-800 px-1.5 py-0.5 rounded">@{{nazov_webinara}}</code> <code class="text-xs bg-ink-100 dark:bg-ink-800 px-1.5 py-0.5 rounded">@{{datum_webinara}}</code> <code class="text-xs bg-ink-100 dark:bg-ink-800 px-1.5 py-0.5 rounded">@{{link_na_webinar}}</code></p>

                @php $templates = $webinar->emailTemplates()->orderBy('delay_minutes')->get(); @endphp

                @if($templates->isEmpty())
                    <p class="text-sm text-ink-400 py-4 mb-4">Zatiaľ žiadne šablóny. Pridajte prvú:</p>
                @else
                    <div class="space-y-3 mb-6">
                        @foreach($templates as $tpl)
                        <div class="border border-ink-200 dark:border-ink-600 rounded-xl p-4" x-data="{ editing: false }">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $tpl->is_active ? 'bg-success-50 text-success-700' : 'bg-ink-100 text-ink-400' }}">
                                        {{ $tpl->is_active ? 'Aktívna' : 'Neaktívna' }}
                                    </span>
                                    <span class="text-sm font-medium text-ink-800 dark:text-white">{{ $tpl->trigger_type }}</span>
                                    <span class="text-xs text-ink-400">({{ $tpl->delay_minutes > 0 ? '+' . $tpl->delay_minutes . ' min' : $tpl->delay_minutes . ' min' }})</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="editing = !editing" class="text-sm text-violet-600 hover:text-violet-500">Upraviť</button>
                                    <form method="POST" action="{{ route('dashboard.webinars.emails.destroy', [$webinar, $tpl]) }}" onsubmit="return confirm('Zmazať?')">
                                        @csrf @method('DELETE')
                                        <button class="text-sm text-danger-600 hover:text-danger-500">Zmazať</button>
                                    </form>
                                </div>
                            </div>
                            <p class="text-sm text-ink-600 dark:text-ink-400 mt-1">{{ $tpl->subject }}</p>

                            <div x-show="editing" x-cloak class="mt-4 border-t border-ink-200 dark:border-ink-600 pt-4">
                                <form method="POST" action="{{ route('dashboard.webinars.emails.update', [$webinar, $tpl]) }}" class="space-y-3">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block text-xs text-ink-500 mb-1">Predmet</label>
                                        <input type="text" name="subject" value="{{ $tpl->subject }}" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-ink-500 mb-1">Obsah (HTML)</label>
                                        <textarea name="body_html" rows="6" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm font-mono focus:border-violet-600 ring-violet-focus transition-fast">{{ $tpl->body_html }}</textarea>
                                    </div>
                                    <label class="flex items-center gap-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" {{ $tpl->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-ink-300 text-violet-600">
                                        <span class="text-sm text-ink-700 dark:text-ink-300">Aktívna</span>
                                    </label>
                                    <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm rounded-lg transition-fast">Uložiť</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add new template --}}
                <details>
                    <summary class="text-sm text-violet-600 cursor-pointer hover:text-violet-500 font-medium">+ Pridať novú šablónu</summary>
                    <form method="POST" action="{{ route('dashboard.webinars.emails.store', $webinar) }}" class="mt-3 space-y-3 p-4 border border-ink-200 dark:border-ink-600 rounded-xl">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-ink-500 mb-1">Typ triggeru</label>
                                <select name="trigger_type" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                                    <option value="registration_confirmed">Potvrdenie registrácie</option>
                                    <option value="reminder_24h">Pripomienka 24h</option>
                                    <option value="reminder_1h">Pripomienka 1h</option>
                                    <option value="reminder_15m">Pripomienka 15 min</option>
                                    <option value="reminder_5m">Pripomienka 5 min</option>
                                    <option value="missed">Zmeškaný webinár</option>
                                    <option value="replay">Replay</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-ink-500 mb-1">Oneskorenie (min, záporné = pred)</label>
                                <input type="number" name="delay_minutes" value="0" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-ink-500 mb-1">Predmet</label>
                            <input type="text" name="subject" required class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                        </div>
                        <div>
                            <label class="block text-xs text-ink-500 mb-1">Obsah (HTML)</label>
                            <textarea name="body_html" rows="5" required class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm font-mono focus:border-violet-600 ring-violet-focus transition-fast"></textarea>
                        </div>
                        <input type="hidden" name="is_active" value="1">
                        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Vytvoriť šablónu</button>
                    </form>
                </details>
            </div>
        </div>

        {{-- Tab: Tracking --}}
        <div x-show="tab === 'tracking'" x-cloak>
            <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Tracking pixely</h3>

                <form method="POST" action="{{ route('dashboard.webinars.tracking.store', $webinar) }}" class="space-y-4 mb-6 p-4 border border-ink-200 dark:border-ink-600 rounded-xl">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-ink-500 mb-1">Typ pixelu</label>
                            <select name="pixel_type" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                                <option value="facebook">Facebook Pixel</option>
                                <option value="ga4">Google Analytics 4</option>
                                <option value="google_ads">Google Ads</option>
                                <option value="tiktok">TikTok Pixel</option>
                                <option value="custom">Custom script</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-ink-500 mb-1">Pixel ID / Measurement ID</label>
                            <input type="text" name="pixel_id" placeholder="Napr. G-XXXXXXXX" class="w-full px-3 py-2 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-ink-500 mb-1">Stránky kde odpaľovať</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['registration' => 'Registrácia', 'thankyou' => 'Ďakujeme', 'webinar_room' => 'Webinár'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="page_placement[]" value="{{ $val }}" checked class="w-4 h-4 rounded border-ink-300 text-violet-600">
                                <span class="text-sm text-ink-700 dark:text-ink-300">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="is_active" value="1">
                    <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Pridať pixel</button>
                </form>

                @php $pixels = $webinar->trackingPixels()->get(); @endphp
                @if($pixels->isEmpty())
                    <p class="text-sm text-ink-400">Zatiaľ žiadne tracking pixely.</p>
                @else
                    <div class="space-y-3">
                        @foreach($pixels as $pixel)
                        <div class="flex items-center justify-between p-3 border border-ink-200 dark:border-ink-600 rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex px-2.5 py-1 bg-violet-50 dark:bg-violet-900/20 text-violet-700 text-xs font-medium rounded-full">{{ strtoupper($pixel->pixel_type) }}</span>
                                <span class="text-sm text-ink-800 dark:text-white font-mono">{{ $pixel->pixel_id ?: 'Custom script' }}</span>
                                <span class="text-xs text-ink-400">{{ implode(', ', $pixel->page_placement ?? []) }}</span>
                            </div>
                            <form method="POST" action="{{ route('dashboard.webinars.tracking.destroy', [$webinar, $pixel]) }}" onsubmit="return confirm('Zmazať?')">
                                @csrf @method('DELETE')
                                <button class="p-1 text-ink-400 hover:text-danger-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab: Embed --}}
        <div x-show="tab === 'embed'" x-cloak>
            <div class="max-w-3xl space-y-6">
                @if($webinar->isEvergreen())
                {{-- Registration form embed --}}
                <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                    <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-2">Registračný formulár (embed)</h3>
                    <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">Vložte tento kód na svoju stránku — zobrazí registračný formulár pre tento webinár.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs text-ink-500 mb-1.5">Script varianta (odporúčaná)</label>
                            <div class="relative">
                                <pre class="px-4 py-3 bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-lg text-sm font-mono text-ink-800 dark:text-ink-200 overflow-x-auto whitespace-pre-wrap break-all">&lt;script src="{{ url('/js/embed.js') }}" data-tenant="{{ auth()->user()->tenant->slug }}" data-webinar="{{ $webinar->slug }}"&gt;&lt;/script&gt;</pre>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-ink-500 mb-1.5">iFrame varianta</label>
                            <pre class="px-4 py-3 bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-lg text-sm font-mono text-ink-800 dark:text-ink-200 overflow-x-auto whitespace-pre-wrap break-all">&lt;iframe src="{{ route('embed.register', ['tenantSlug' => auth()->user()->tenant->slug, 'webinarSlug' => $webinar->slug]) }}" style="width:100%;min-height:400px;border:none;border-radius:12px" loading="lazy"&gt;&lt;/iframe&gt;</pre>
                        </div>
                        <div>
                            <label class="block text-xs text-ink-500 mb-1.5">Priamy link na registračnú stránku</label>
                            <pre class="px-4 py-3 bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-lg text-sm font-mono text-violet-600 overflow-x-auto whitespace-pre-wrap break-all">{{ route('public.register', ['tenantSlug' => auth()->user()->tenant->slug, 'webinarSlug' => $webinar->slug]) }}</pre>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Smart video player embed --}}
                <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
                    <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-2">Video prehrávač (embed)</h3>
                    <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">Vložte inteligentný prehrávač s CTA na akúkoľvek stránku.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs text-ink-500 mb-1.5">Script varianta</label>
                            <pre class="px-4 py-3 bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-lg text-sm font-mono text-ink-800 dark:text-ink-200 overflow-x-auto whitespace-pre-wrap break-all">&lt;script src="{{ url('/js/embed.js') }}" data-tenant="{{ auth()->user()->tenant->slug }}" data-smart-video="{{ $webinar->id }}"&gt;&lt;/script&gt;</pre>
                        </div>
                        <div>
                            <label class="block text-xs text-ink-500 mb-1.5">iFrame varianta</label>
                            <pre class="px-4 py-3 bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-lg text-sm font-mono text-ink-800 dark:text-ink-200 overflow-x-auto whitespace-pre-wrap break-all">&lt;iframe src="{{ route('embed.player', $webinar->id) }}" style="width:100%;aspect-ratio:16/9;border:none;border-radius:12px" loading="lazy" allow="autoplay;fullscreen" allowfullscreen&gt;&lt;/iframe&gt;</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Analytics --}}
        <div x-show="tab === 'analytics'" x-cloak>
            <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 text-center">
                <i data-lucide="bar-chart-3" class="w-12 h-12 text-ink-300 mx-auto mb-4"></i>
                <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-2">Analytika webinára</h3>
                <p class="text-ink-500 dark:text-ink-400 text-sm mb-4">Detailné štatistiky, funnel, UTM zdroje a export.</p>
                <a href="{{ route('dashboard.webinars.analytics', $webinar) }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Otvoriť analytiku
                </a>
            </div>
        </div>
    </div>
@endsection
