@extends('layouts.app')

@section('title', 'Webinár skončil')

@section('body')
<div class="min-h-screen bg-ink-900 flex items-center justify-center px-4">
    <div class="text-center max-w-md">
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-ink-800 flex items-center justify-center">
            <svg class="w-8 h-8 text-ink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h1 class="font-heading text-2xl font-bold text-white mb-3">Tento webinár už skončil</h1>
        <p class="text-ink-400">Ďakujeme za váš záujem. Webinár bol dostupný len obmedzený čas.</p>
    </div>
</div>
@endsection
