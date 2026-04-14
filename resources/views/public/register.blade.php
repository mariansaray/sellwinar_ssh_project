@extends('layouts.app')

@section('title', ($config['headline'] ?? $webinar->name) . ' — Sellwinar')

@section('body')
<div class="min-h-screen" style="background-color: {{ $config['background_color'] ?? '#FFFFFF' }}">
    <div class="max-w-md mx-auto px-4 py-12 sm:py-20">

        {{-- Logo --}}
        @if(!empty($config['logo_url']))
            <div class="text-center mb-8">
                <img src="{{ $config['logo_url'] }}" alt="" class="h-12 mx-auto">
            </div>
        @endif

        {{-- Headline --}}
        <h1 class="font-heading text-3xl sm:text-4xl font-bold text-ink-800 text-center mb-3 leading-tight">
            {{ $config['headline'] ?? $webinar->name }}
        </h1>

        @if(!empty($config['subheadline']))
            <p class="text-lg text-ink-600 text-center mb-6">{{ $config['subheadline'] }}</p>
        @endif

        {{-- Countdown / Next time badge --}}
        @if(!empty($config['show_countdown']) && $nextTime)
            <div class="flex items-center justify-center gap-2 mb-6">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-success-50 text-success-700 text-sm font-medium rounded-full">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $nextTime->format('d.m.Y H:i') }}
                </span>
            </div>
        @endif

        {{-- Description / Benefits --}}
        @if(!empty($config['description']))
            <div class="text-sm text-ink-600 mb-6 leading-relaxed">{!! nl2br(e($config['description'])) !!}</div>
        @endif

        {{-- Registration form --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-ink-100">
            @if($errors->any())
                <div class="mb-4 bg-danger-50 border-l-4 border-danger-500 rounded-lg p-3">
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-danger-700">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('public.register', ['tenantSlug' => $tenant->slug, 'webinarSlug' => $webinar->slug]) }}">
                @csrf

                {{-- UTM hidden fields --}}
                <input type="hidden" name="utm_source" value="{{ request('utm_source') }}">
                <input type="hidden" name="utm_medium" value="{{ request('utm_medium') }}">
                <input type="hidden" name="utm_campaign" value="{{ request('utm_campaign') }}">
                <input type="hidden" name="utm_term" value="{{ request('utm_term') }}">
                <input type="hidden" name="utm_content" value="{{ request('utm_content') }}">

                <div class="space-y-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-ink-700 mb-1.5">Meno</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                               class="w-full px-4 py-3 border border-ink-200 rounded-lg text-sm focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-600/20 transition-all"
                               placeholder="Vaše meno">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-ink-700 mb-1.5">E-mail <span class="text-danger-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border border-ink-200 rounded-lg text-sm focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-600/20 transition-all"
                               placeholder="vas@email.sk">
                    </div>

                    @if(!empty($config['require_phone']))
                    <div>
                        <label for="phone" class="block text-sm font-medium text-ink-700 mb-1.5">Telefón</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full px-4 py-3 border border-ink-200 rounded-lg text-sm focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-600/20 transition-all"
                               placeholder="+421 ...">
                    </div>
                    @endif

                    <button type="submit"
                            class="w-full py-3.5 text-white font-semibold text-base rounded-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-px"
                            style="background: linear-gradient(135deg, {{ $config['primary_color'] ?? '#6C3AED' }}, {{ $config['primary_color'] ?? '#7C4DFF' }}ee)">
                        {{ $config['cta_text'] ?? 'Registrovať sa zadarmo' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Social proof --}}
        @if(!empty($config['show_social_proof']) && $registrantCount > 0)
            <p class="text-center text-sm text-ink-400 mt-4">
                <svg class="w-4 h-4 inline text-success-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Už {{ $registrantCount }} {{ $registrantCount === 1 ? 'osoba sa registrovala' : 'ľudí sa registrovalo' }}
            </p>
        @endif

    </div>
</div>

@if(!empty($config['custom_css']))
<style>{{ $config['custom_css'] }}</style>
@endif

<x-tracking-pixels :webinar="$webinar" placement="registration" />
@endsection
