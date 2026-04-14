@extends('layouts.app')

@section('body')
<div class="w-full" style="background: {{ $playerConfig['backgroundColor'] ?? '#000' }}">
    <x-video-player
        :config="$playerConfig"
        :webinar-id="$webinar->id"
    />

    {{-- CTA --}}
    @if(!empty($ctaConfig['text']) && !empty($ctaConfig['url']))
    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, {{ ($ctaConfig['show_at_seconds'] ?? 9999) * 1000 }})"
         x-show="show" x-transition class="px-2 pb-2">
        <a href="{{ $ctaConfig['url'] }}" target="_blank" rel="noopener"
           class="block w-full py-3 text-center text-white font-bold text-base rounded-lg"
           style="background: {{ $ctaConfig['button_color'] ?? '#6C3AED' }}; color: {{ $ctaConfig['text_color'] ?? '#FFF' }}">
            {{ $ctaConfig['text'] }}
        </a>
    </div>
    @endif
</div>

<script>
    function sendHeight() {
        window.parent.postMessage({ type: 'sellwinar-resize', id: '{{ $webinar->id }}', height: document.body.scrollHeight }, '*');
    }
    window.addEventListener('load', sendHeight);
    new MutationObserver(sendHeight).observe(document.body, { childList: true, subtree: true });
</script>
@endsection
