@extends('layouts.guest')

@section('title', 'Prihlásenie — Sellwinar')

@section('content')
    <h1 class="font-heading text-2xl font-bold text-ink-800 dark:text-white mb-2">Prihláste sa</h1>
    <p class="text-ink-500 dark:text-ink-400 text-sm mb-6">Zadajte svoje prihlasovacie údaje</p>

    @if($errors->any())
        <div class="mb-4 bg-danger-50 dark:bg-danger-700/20 border-l-4 border-danger-500 rounded-lg p-3">
            @foreach($errors->all() as $error)
                <p class="text-sm text-danger-700 dark:text-danger-400">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 bg-success-50 dark:bg-success-700/20 border-l-4 border-success-500 rounded-lg p-3">
            <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                   placeholder="vas@email.sk">
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Heslo</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                   placeholder="Vaše heslo">
        </div>

        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-ink-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm text-ink-600 dark:text-ink-400">Zapamätať ma</span>
            </label>
        </div>

        <button type="submit"
                class="w-full py-2.5 px-6 bg-violet-600 hover:bg-violet-500 active:bg-violet-700 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            Prihlásiť sa
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-500 dark:text-ink-400">
        Nemáte účet?
        <a href="{{ route('register') }}" class="text-violet-600 hover:text-violet-500 font-medium">Zaregistrujte sa</a>
    </p>
@endsection
