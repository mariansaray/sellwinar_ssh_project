@extends('layouts.guest')

@section('title', 'Inštalácia — Sellwinar')

@section('content')
    <h1 class="font-heading text-2xl font-bold text-ink-800 dark:text-white mb-2">Inštalácia Sellwinar</h1>
    <p class="text-ink-500 dark:text-ink-400 text-sm mb-6">Kliknite na tlačidlo pre nastavenie databázy a vytvorenie admin účtu.</p>

    @if(session('error'))
        <div class="mb-4 bg-danger-50 dark:bg-danger-700/20 border-l-4 border-danger-500 rounded-lg p-3">
            <p class="text-sm text-danger-700 dark:text-danger-400">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('install.run') }}">
        @csrf
        <button type="submit"
                class="w-full py-2.5 px-6 bg-violet-600 hover:bg-violet-500 active:bg-violet-700 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            Spustiť inštaláciu
        </button>
    </form>

    <div class="mt-6 p-4 bg-ink-50 dark:bg-ink-800 rounded-lg">
        <p class="text-xs text-ink-500 dark:text-ink-400">
            <strong>Čo sa stane:</strong> Vytvoria sa databázové tabuľky, billing plány a Super Admin účet (admin@sellwinar.com / admin123).
        </p>
    </div>
@endsection
