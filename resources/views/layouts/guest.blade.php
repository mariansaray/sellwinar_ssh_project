@extends('layouts.app')

@section('body')
<div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
    {{-- Logo --}}
    <a href="/" class="mb-8">
        <div class="flex items-center gap-1">
            <span class="font-heading text-3xl font-bold text-violet-600">Sell</span>
            <span class="font-heading text-3xl font-bold text-ink-800 dark:text-white">inar</span>
        </div>
    </a>

    {{-- Card --}}
    <div class="w-full max-w-md bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-2xl shadow-md p-8">
        @yield('content')
    </div>

    {{-- Footer --}}
    <p class="mt-8 text-sm text-ink-400">&copy; {{ date('Y') }} Sellwinar. Všetky práva vyhradené.</p>
</div>
@endsection
