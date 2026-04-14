@extends('layouts.app')

@section('title', $webinar->name . ' — LIVE')

@section('body')
<div class="min-h-screen bg-ink-900 text-white" x-data="webinarRoom()" x-init="init()">

    {{-- Top bar --}}
    <div class="h-12 bg-ink-800 border-b border-ink-700 flex items-center justify-between px-4">
        <h1 class="font-heading text-sm font-semibold text-white truncate">{{ $webinar->name }}</h1>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-danger-600/20 text-danger-400 text-xs font-semibold rounded-full">
                <span class="w-2 h-2 bg-danger-500 rounded-full animate-pulse"></span>
                LIVE
                <span x-text="viewerCount" class="ml-1"></span> sleduje
            </span>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex flex-col lg:flex-row max-w-[1400px] mx-auto">
        {{-- Video area --}}
        <div class="flex-1 p-4">
            <div class="max-w-[960px] mx-auto">
                {{-- Video player --}}
                <x-video-player
                    :config="$playerConfig"
                    :webinar-id="$webinar->id"
                    :registrant-id="$registrant->id"
                    :session-id="'sess_' . substr($registrant->access_token, 0, 16)"
                />

                {{-- CTA Button --}}
                @if(!empty($ctaConfig['text']) && !empty($ctaConfig['url']))
                <div x-show="showCta" x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-4">
                    <a href="{{ $ctaConfig['url'] }}" target="_blank" rel="noopener"
                       @click="trackCtaClick()"
                       class="block w-full py-4 text-center text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-px"
                       style="background: linear-gradient(135deg, {{ $ctaConfig['button_color'] ?? '#6C3AED' }}, {{ $ctaConfig['button_color'] ?? '#7C4DFF' }}ee); color: {{ $ctaConfig['text_color'] ?? '#FFFFFF' }}">
                        {{ $ctaConfig['text'] }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Chat panel --}}
        @if($webinar->chat_enabled)
        <div class="w-full lg:w-[320px] lg:min-h-[calc(100vh-48px)] bg-ink-800 border-l border-ink-700 flex flex-col">
            {{-- Chat header --}}
            <div class="px-4 py-3 border-b border-ink-700">
                <h3 class="font-heading text-sm font-semibold text-white">Chat</h3>
            </div>

            {{-- Chat messages --}}
            <div x-ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[400px] lg:max-h-none">
                <template x-for="msg in messages" :key="msg.id || msg.time">
                    <div class="flex gap-2.5">
                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white"
                             :style="'background: ' + getAvatarColor(msg.name)">
                            <span x-text="msg.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold" :class="msg.isAdmin ? 'text-violet-400' : 'text-ink-300'" x-text="msg.name"></span>
                            <p class="text-sm text-ink-100 mt-0.5" x-text="msg.text"></p>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Chat input --}}
            <div class="p-3 border-t border-ink-700">
                <form @submit.prevent="sendMessage()" class="flex gap-2">
                    <input x-model="newMessage" type="text" placeholder="Napíšte správu..."
                           class="flex-1 px-3 py-2 bg-ink-700 border border-ink-600 rounded-lg text-sm text-white placeholder-ink-400 focus:border-violet-600 focus:outline-none transition-all">
                    <button type="submit" class="px-3 py-2 bg-violet-600 hover:bg-violet-500 text-white rounded-lg transition-fast">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Purchase alert toast --}}
    <div x-show="currentAlert" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 left-6 z-50 bg-white dark:bg-ink-700 rounded-xl shadow-xl p-4 max-w-xs border border-ink-200 dark:border-ink-600">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-success-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-ink-800 dark:text-white" x-text="currentAlert?.name + ' si práve zakúpil/a'"></p>
                <p class="text-xs text-ink-500 dark:text-ink-400" x-text="currentAlert?.product"></p>
            </div>
        </div>
    </div>

    {{-- Mobile sticky CTA --}}
    @if(!empty($ctaConfig['sticky_on_mobile']) && !empty($ctaConfig['text']) && !empty($ctaConfig['url']))
    <div x-show="showCta" class="lg:hidden fixed bottom-0 left-0 right-0 z-40 p-3 bg-ink-900/95 border-t border-ink-700">
        <a href="{{ $ctaConfig['url'] }}" target="_blank" rel="noopener"
           @click="trackCtaClick()"
           class="block w-full py-3 text-center text-white font-bold text-base rounded-lg"
           style="background: linear-gradient(135deg, {{ $ctaConfig['button_color'] ?? '#6C3AED' }}, {{ $ctaConfig['button_color'] ?? '#7C4DFF' }}ee)">
            {{ $ctaConfig['text'] }}
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script src="/js/video-player.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('webinarRoom', () => ({
        // Chat
        messages: [],
        newMessage: '',
        lastMessageTime: 0,

        // CTA
        showCta: false,
        ctaShowAt: {{ $ctaConfig['show_at_seconds'] ?? 99999 }},
        ctaHideAt: {{ $ctaConfig['hide_at_seconds'] ?? 'null' }},

        // Viewer count
        viewerCount: {{ ($chatConfig->viewer_count_min ?? 45) + rand(0, ($chatConfig->viewer_count_max ?? 120) - ($chatConfig->viewer_count_min ?? 45)) }},

        // Purchase alerts
        currentAlert: null,
        alerts: @json($webinar->purchaseAlerts()->orderBy('display_at_seconds')->get(['buyer_name as name', 'product_name as product', 'display_at_seconds as time'])),

        // Session
        sessionId: 'sess_{{ substr($registrant->access_token, 0, 16) }}',
        webinarId: {{ $webinar->id }},
        registrantId: {{ $registrant->id }},

        init() {
            // Poll for chat messages every 3 seconds
            setInterval(() => this.fetchChat(), 3000);

            // Update viewer count every 15-45 seconds
            this.updateViewerCount();
            setInterval(() => this.updateViewerCount(), (15 + Math.random() * 30) * 1000);

            // Watch video time for CTA and alerts
            setInterval(() => this.checkTimedElements(), 1000);
        },

        fetchChat() {
            // Get current video time from Alpine video player if exists
            const videoTime = this.getCurrentVideoTime();

            fetch(`/api/chat?webinar_id=${this.webinarId}&session_id=${this.sessionId}&current_second=${Math.floor(videoTime)}&since=${this.lastMessageTime}`)
                .then(r => r.json())
                .then(data => {
                    if (data.messages && data.messages.length) {
                        this.messages.push(...data.messages);
                        this.lastMessageTime = Date.now();
                        this.$nextTick(() => {
                            if (this.$refs.chatContainer) {
                                this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                            }
                        });
                    }
                })
                .catch(() => {});
        },

        sendMessage() {
            if (!this.newMessage.trim()) return;

            const msg = this.newMessage.trim();
            this.newMessage = '';

            // Show locally immediately
            this.messages.push({
                id: 'local_' + Date.now(),
                name: '{{ $registrant->first_name ?? "Vy" }}',
                text: msg,
                isOwn: true,
            });

            fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    webinar_id: this.webinarId,
                    registrant_id: this.registrantId,
                    session_id: this.sessionId,
                    message: msg,
                    sender_name: '{{ $registrant->first_name ?? "Divák" }}',
                }),
            }).catch(() => {});

            this.$nextTick(() => {
                if (this.$refs.chatContainer) {
                    this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                }
            });
        },

        checkTimedElements() {
            const t = this.getCurrentVideoTime();

            // CTA visibility
            if (t >= this.ctaShowAt) {
                if (!this.showCta) {
                    this.showCta = true;
                    this.trackEvent('cta_show');
                }
            }
            if (this.ctaHideAt && t >= this.ctaHideAt) {
                this.showCta = false;
            }

            // Purchase alerts
            for (const alert of this.alerts) {
                if (!alert._shown && t >= alert.time) {
                    alert._shown = true;
                    this.currentAlert = alert;
                    setTimeout(() => { this.currentAlert = null; }, 5000);
                }
            }
        },

        updateViewerCount() {
            const min = {{ $chatConfig->viewer_count_min ?? 45 }};
            const max = {{ $chatConfig->viewer_count_max ?? 120 }};
            this.viewerCount = min + Math.floor(Math.random() * (max - min));
        },

        getCurrentVideoTime() {
            // Try to read from the video player Alpine component
            const playerEl = document.querySelector('[x-data*="videoPlayer"]');
            if (playerEl && playerEl.__x) {
                return playerEl.__x.$data.currentTime || 0;
            }
            return 0;
        },

        trackCtaClick() {
            this.trackEvent('cta_click');
        },

        trackEvent(type) {
            fetch('/api/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    webinar_id: this.webinarId,
                    session_id: this.sessionId,
                    registrant_id: this.registrantId,
                    event_type: type,
                    event_data: { video_second: Math.floor(this.getCurrentVideoTime()) },
                }),
            }).catch(() => {});
        },

        getAvatarColor(name) {
            const colors = ['#6C3AED', '#059669', '#D97706', '#DC2626', '#2563EB', '#7C4DFF', '#B45309', '#047857'];
            let hash = 0;
            for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
            return colors[Math.abs(hash) % colors.length];
        },
    }));
});
</script>
@endpush
@endsection
