@props([
    'config' => [],
    'webinarId' => null,
    'registrantId' => null,
    'sessionId' => null,
])

@php
    $playerConfig = array_merge([
        'primaryColor' => '#6C3AED',
        'backgroundColor' => '#000000',
        'showPlayPause' => true,
        'showProgress' => true,
        'allowSeeking' => false,
        'fakeProgressBar' => false,
        'fakeDurationSeconds' => null,
        'showVolume' => true,
        'showFullscreen' => true,
        'showSpeed' => false,
        'autoplay' => true,
        'startMuted' => true,
    ], $config);

    $jsConfig = json_encode(array_merge($playerConfig, [
        'source' => $config['source'] ?? 'custom',
        'videoUrl' => $config['videoUrl'] ?? '',
        'webinarId' => $webinarId,
        'registrantId' => $registrantId,
        'sessionId' => $sessionId,
    ]));
@endphp

<style>
    .sellwinar-player iframe { width: 100% !important; height: 100% !important; position: absolute; top: 0; left: 0; }
    .sellwinar-player video { width: 100% !important; height: 100% !important; object-fit: contain; }
</style>
<div x-data="videoPlayer({{ $jsConfig }})"
     x-ref="playerWrapper"
     class="sellwinar-player relative w-full bg-black rounded-xl overflow-hidden select-none"
     style="aspect-ratio: 16/9;">

    {{-- Video container --}}
    <div x-ref="videoContainer" class="absolute inset-0 w-full h-full">
        {{-- Player is injected here by JS --}}
    </div>

    {{-- Loading spinner --}}
    <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-black/60 z-20">
        <div class="w-12 h-12 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
    </div>

    {{-- Unmute overlay --}}
    <div x-show="showUnmuteOverlay" @click="clickUnmute()"
         class="absolute inset-0 z-30 flex items-center justify-center bg-black/30 cursor-pointer">
        <div class="flex items-center gap-3 px-6 py-3 bg-white/90 dark:bg-ink-800/90 rounded-full shadow-lg">
            <svg class="w-6 h-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072M12 6l-4 4H4v4h4l4 4V6z"/>
            </svg>
            <span class="text-sm font-semibold text-ink-800 dark:text-white">Klikni pre zvuk</span>
        </div>
    </div>

    {{-- Click area for play/pause --}}
    <div @click="togglePlay()" class="absolute inset-0 z-10 cursor-pointer"></div>

    {{-- Custom controls --}}
    <div class="absolute bottom-0 left-0 right-0 z-20 bg-gradient-to-t from-black/80 to-transparent px-4 pb-3 pt-10"
         x-show="!loading" x-transition>

        {{-- Progress bar --}}
        <template x-if="showControls.progress">
            <div @click="seekFromProgress($event)"
                 :class="allowSeeking ? 'cursor-pointer' : 'cursor-default'"
                 class="relative w-full h-1.5 bg-white/20 rounded-full mb-3 group hover:h-2.5 transition-all">
                {{-- Buffered --}}
                <div class="absolute top-0 left-0 h-full bg-white/20 rounded-full"
                     :style="'width: ' + (duration > 0 ? (buffered / duration * 100) : 0) + '%'"></div>
                {{-- Progress --}}
                <div class="absolute top-0 left-0 h-full rounded-full transition-all"
                     :style="'width: ' + progressPercent + '%; background: ' + primaryColor"></div>
                {{-- Thumb --}}
                <div x-show="allowSeeking"
                     class="absolute top-1/2 -translate-y-1/2 w-3.5 h-3.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow"
                     :style="'left: ' + progressPercent + '%; background: ' + primaryColor + '; transform: translate(-50%, -50%)'"></div>
            </div>
        </template>

        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                {{-- Play/Pause --}}
                <template x-if="showControls.playPause">
                    <button @click.stop="togglePlay()" class="text-white hover:text-white/80 transition-fast">
                        <svg x-show="!playing" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <svg x-show="playing" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                        </svg>
                    </button>
                </template>

                {{-- Volume --}}
                <template x-if="showControls.volume">
                    <div class="flex items-center gap-2">
                        <button @click.stop="toggleMute()" class="text-white hover:text-white/80 transition-fast">
                            <svg x-show="!muted" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072M12 6l-4 4H4v4h4l4 4V6z"/>
                            </svg>
                            <svg x-show="muted" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707A1 1 0 0112 5v14a1 1 0 01-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Time display --}}
                <span class="text-white/80 text-xs font-mono tabular-nums">
                    <span x-text="formatTime(displayTime)"></span>
                    <span class="text-white/40"> / </span>
                    <span x-text="formatTime(displayDuration)"></span>
                </span>
            </div>

            <div class="flex items-center gap-3">
                {{-- Speed --}}
                <template x-if="showControls.speed">
                    <div class="relative" @click.stop>
                        <button @click="showSpeedMenu = !showSpeedMenu"
                                class="text-white/80 hover:text-white text-xs font-mono transition-fast"
                                x-text="speedRate + 'x'"></button>
                        <div x-show="showSpeedMenu" @click.outside="showSpeedMenu = false"
                             class="absolute bottom-8 right-0 bg-ink-800 border border-ink-600 rounded-lg py-1 shadow-lg min-w-[80px]">
                            <template x-for="rate in [0.5, 0.75, 1, 1.25, 1.5, 2]" :key="rate">
                                <button @click="setSpeed(rate)"
                                        :class="speedRate === rate ? 'text-violet-400' : 'text-white/80'"
                                        class="block w-full text-left px-3 py-1.5 text-xs hover:bg-ink-700 transition-fast"
                                        x-text="rate + 'x'"></button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Fullscreen --}}
                <template x-if="showControls.fullscreen">
                    <button @click.stop="toggleFullscreen()" class="text-white hover:text-white/80 transition-fast">
                        <svg x-show="!fullscreen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5h-4m4 0v-4m0 4l-5-5"/>
                        </svg>
                        <svg x-show="fullscreen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9L4 4m0 0v4m0-4h4m7 0l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m7 0l5-5m0 0v4m0-4h-4"/>
                        </svg>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

{{-- video-player.js is loaded in layouts/app.blade.php head --}}
