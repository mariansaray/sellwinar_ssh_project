/**
 * Sellwinar Video Player — Alpine.js component
 * Supports YouTube, Vimeo, and custom (HTML5) video sources
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('videoPlayer', (config = {}) => ({
        // State
        playing: false,
        muted: config.startMuted !== false,
        currentTime: 0,
        duration: 0,
        volume: 1,
        buffered: 0,
        fullscreen: false,
        loading: true,
        showUnmuteOverlay: false,
        speedRate: 1,
        showSpeedMenu: false,

        // Config
        source: config.source || 'custom', // youtube, vimeo, custom
        videoUrl: config.videoUrl || '',
        primaryColor: config.primaryColor || '#6C3AED',
        allowSeeking: config.allowSeeking !== false,
        fakeProgressBar: config.fakeProgressBar || false,
        fakeDuration: config.fakeDurationSeconds || null,
        autoplay: config.autoplay !== false,
        showControls: {
            playPause: config.showPlayPause !== false,
            progress: config.showProgress !== false,
            volume: config.showVolume !== false,
            fullscreen: config.showFullscreen !== false,
            speed: config.showSpeed || false,
        },

        // Internal references
        player: null, // YouTube/Vimeo player instance
        videoEl: null, // HTML5 video element
        trackingInterval: null,
        sessionId: config.sessionId || this._generateSessionId(),
        webinarId: config.webinarId || null,
        registrantId: config.registrantId || null,

        init() {
            this.$nextTick(() => {
                if (this.source === 'youtube') {
                    this._initYouTube();
                } else if (this.source === 'vimeo') {
                    this._initVimeo();
                } else {
                    this._initHTML5();
                }
            });
        },

        // ---- YouTube ----
        _initYouTube() {
            const videoId = this._extractYouTubeId(this.videoUrl);
            if (!videoId) { this.loading = false; return; }

            const containerId = 'yt-player-' + Date.now();
            this.$refs.videoContainer.innerHTML = `<div id="${containerId}"></div>`;

            const loadPlayer = () => {
                this.player = new YT.Player(containerId, {
                    videoId: videoId,
                    playerVars: {
                        autoplay: this.autoplay ? 1 : 0,
                        mute: this.muted ? 1 : 0,
                        controls: 0,
                        modestbranding: 1,
                        rel: 0,
                        showinfo: 0,
                        iv_load_policy: 3,
                        disablekb: 1,
                        playsinline: 1,
                    },
                    events: {
                        onReady: (e) => {
                            this.duration = e.target.getDuration();
                            this.loading = false;
                            if (this.autoplay && this.muted) {
                                this.showUnmuteOverlay = true;
                            }
                            this._startTimeSync();
                            this._trackEvent('video_load');
                        },
                        onStateChange: (e) => {
                            this.playing = e.data === YT.PlayerState.PLAYING;
                            if (e.data === YT.PlayerState.ENDED) {
                                this._trackEvent('video_complete');
                            }
                        }
                    }
                });
            };

            if (typeof YT !== 'undefined' && YT.Player) {
                loadPlayer();
            } else {
                window.onYouTubeIframeAPIReady = loadPlayer;
                const tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(tag);
            }
        },

        // ---- Vimeo ----
        _initVimeo() {
            const containerId = 'vimeo-player-' + Date.now();
            this.$refs.videoContainer.innerHTML = `<div id="${containerId}"></div>`;

            const loadPlayer = () => {
                this.player = new Vimeo.Player(containerId, {
                    url: this.videoUrl,
                    autoplay: this.autoplay,
                    muted: this.muted,
                    controls: false,
                    responsive: true,
                });

                this.player.on('loaded', () => {
                    this.player.getDuration().then(d => { this.duration = d; });
                    this.loading = false;
                    if (this.autoplay && this.muted) this.showUnmuteOverlay = true;
                    this._startTimeSync();
                    this._trackEvent('video_load');
                });
                this.player.on('play', () => { this.playing = true; });
                this.player.on('pause', () => { this.playing = false; });
                this.player.on('ended', () => { this._trackEvent('video_complete'); });
                this.player.on('timeupdate', (data) => {
                    this.currentTime = data.seconds;
                });
            };

            if (typeof Vimeo !== 'undefined') {
                loadPlayer();
            } else {
                const tag = document.createElement('script');
                tag.src = 'https://player.vimeo.com/api/player.js';
                tag.onload = loadPlayer;
                document.head.appendChild(tag);
            }
        },

        // ---- HTML5 ----
        _initHTML5() {
            const video = document.createElement('video');
            video.src = this.videoUrl;
            video.playsInline = true;
            video.preload = 'metadata';
            video.className = 'w-full h-full object-contain';
            if (this.autoplay) video.autoplay = true;
            if (this.muted) video.muted = true;

            this.$refs.videoContainer.appendChild(video);
            this.videoEl = video;

            video.addEventListener('loadedmetadata', () => {
                this.duration = video.duration;
                this.loading = false;
                if (this.autoplay && this.muted) this.showUnmuteOverlay = true;
                this._startTimeSync();
                this._trackEvent('video_load');
            });
            video.addEventListener('timeupdate', () => { this.currentTime = video.currentTime; });
            video.addEventListener('play', () => { this.playing = true; });
            video.addEventListener('pause', () => { this.playing = false; });
            video.addEventListener('ended', () => { this._trackEvent('video_complete'); });
            video.addEventListener('progress', () => {
                if (video.buffered.length > 0) {
                    this.buffered = video.buffered.end(video.buffered.length - 1);
                }
            });
        },

        // ---- Controls ----
        togglePlay() {
            if (this.playing) {
                this.pause();
            } else {
                this.play();
            }
        },

        play() {
            if (this.source === 'youtube' && this.player) {
                this.player.playVideo();
            } else if (this.source === 'vimeo' && this.player) {
                this.player.play();
            } else if (this.videoEl) {
                this.videoEl.play();
            }
            if (!this._hasTrackedPlay) {
                this._trackEvent('video_play');
                this._hasTrackedPlay = true;
            } else {
                this._trackEvent('video_resume');
            }
        },

        pause() {
            if (this.source === 'youtube' && this.player) {
                this.player.pauseVideo();
            } else if (this.source === 'vimeo' && this.player) {
                this.player.pause();
            } else if (this.videoEl) {
                this.videoEl.pause();
            }
            this._trackEvent('video_pause');
        },

        seek(seconds) {
            if (!this.allowSeeking && seconds > this.currentTime) return;
            if (this.source === 'youtube' && this.player) {
                this.player.seekTo(seconds, true);
            } else if (this.source === 'vimeo' && this.player) {
                this.player.setCurrentTime(seconds);
            } else if (this.videoEl) {
                this.videoEl.currentTime = seconds;
            }
            this.currentTime = seconds;
        },

        seekFromProgress(event) {
            if (!this.allowSeeking) return;
            const rect = event.currentTarget.getBoundingClientRect();
            const pct = (event.clientX - rect.left) / rect.width;
            const targetTime = pct * this.displayDuration;
            // If fake progress, map back to real time
            if (this.fakeProgressBar && this.fakeDuration && this.duration) {
                const realTime = (targetTime / this.fakeDuration) * this.duration;
                this.seek(realTime);
            } else {
                this.seek(targetTime);
            }
        },

        setVolume(val) {
            this.volume = val;
            this.muted = val === 0;
            if (this.source === 'youtube' && this.player) {
                this.player.setVolume(val * 100);
                if (val > 0) this.player.unMute();
            } else if (this.source === 'vimeo' && this.player) {
                this.player.setVolume(val);
            } else if (this.videoEl) {
                this.videoEl.volume = val;
                this.videoEl.muted = val === 0;
            }
        },

        toggleMute() {
            if (this.muted) {
                this.setVolume(this.volume > 0 ? this.volume : 0.8);
                this.muted = false;
                this.showUnmuteOverlay = false;
                if (this.source === 'youtube' && this.player) this.player.unMute();
                if (this.videoEl) this.videoEl.muted = false;
            } else {
                this.muted = true;
                if (this.source === 'youtube' && this.player) this.player.mute();
                if (this.videoEl) this.videoEl.muted = true;
            }
        },

        clickUnmute() {
            this.showUnmuteOverlay = false;
            this.toggleMute();
            if (!this.playing) this.play();
        },

        toggleFullscreen() {
            const container = this.$refs.playerWrapper;
            if (!document.fullscreenElement) {
                container.requestFullscreen().then(() => { this.fullscreen = true; });
            } else {
                document.exitFullscreen().then(() => { this.fullscreen = false; });
            }
        },

        setSpeed(rate) {
            this.speedRate = rate;
            this.showSpeedMenu = false;
            if (this.source === 'youtube' && this.player) {
                this.player.setPlaybackRate(rate);
            } else if (this.source === 'vimeo' && this.player) {
                this.player.setPlaybackRate(rate);
            } else if (this.videoEl) {
                this.videoEl.playbackRate = rate;
            }
        },

        // ---- Computed-like getters ----
        get displayDuration() {
            if (this.fakeProgressBar && this.fakeDuration) return this.fakeDuration;
            return this.duration;
        },

        get displayTime() {
            if (this.fakeProgressBar && this.fakeDuration && this.duration) {
                return (this.currentTime / this.duration) * this.fakeDuration;
            }
            return this.currentTime;
        },

        get progressPercent() {
            if (this.displayDuration === 0) return 0;
            return Math.min((this.displayTime / this.displayDuration) * 100, 100);
        },

        formatTime(seconds) {
            const s = Math.floor(seconds);
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const sec = s % 60;
            if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
            return `${m}:${String(sec).padStart(2, '0')}`;
        },

        // ---- Time sync ----
        _startTimeSync() {
            if (this.source === 'youtube') {
                setInterval(() => {
                    if (this.player && this.player.getCurrentTime) {
                        this.currentTime = this.player.getCurrentTime();
                    }
                }, 250);
            }
            // Tracking every 10 seconds
            this.trackingInterval = setInterval(() => {
                if (this.playing) {
                    this._trackEvent('video_progress');
                }
            }, 10000);
        },

        // ---- Tracking ----
        _trackEvent(eventType) {
            if (!this.webinarId) return;
            const data = {
                webinar_id: this.webinarId,
                session_id: this.sessionId,
                registrant_id: this.registrantId,
                event_type: eventType,
                event_data: {
                    seconds_watched: Math.floor(this.currentTime),
                    total_duration: Math.floor(this.duration),
                    percentage: this.duration > 0 ? Math.round((this.currentTime / this.duration) * 100) : 0,
                },
            };

            fetch('/api/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify(data),
            }).catch(() => {});
        },

        // ---- Helpers ----
        _extractYouTubeId(url) {
            if (!url) return null;
            const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
            return match ? match[1] : null;
        },

        _generateSessionId() {
            return 'sess_' + Date.now().toString(36) + Math.random().toString(36).substr(2, 9);
        },

        _hasTrackedPlay: false,

        destroy() {
            if (this.trackingInterval) clearInterval(this.trackingInterval);
            if (this.source === 'vimeo' && this.player) this.player.destroy();
        }
    }));
});
