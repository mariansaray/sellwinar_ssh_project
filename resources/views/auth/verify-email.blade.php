@extends('layouts.guest')

@section('title', 'Overiť e-mail — Sellwinar')

@section('content')
    <h1 class="font-heading text-2xl font-bold text-ink-800 dark:text-white mb-2">Overte svoj e-mail</h1>
    <p class="text-ink-500 dark:text-ink-400 text-sm mb-6">
        Na váš e-mail sme poslali overovací odkaz. Kliknite naň pre aktiváciu účtu.
    </p>

    @if(session('success'))
        <div class="mb-4 bg-success-50 dark:bg-success-700/20 border-l-4 border-success-500 rounded-lg p-3">
            <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
                class="w-full py-2.5 px-6 bg-violet-600 hover:bg-violet-500 active:bg-violet-700 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            Odoslať znovu
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="w-full text-center text-sm text-ink-500 hover:text-ink-700 dark:text-ink-400 dark:hover:text-ink-200">
            Odhlásiť sa
        </button>
    </form>
@endsection
