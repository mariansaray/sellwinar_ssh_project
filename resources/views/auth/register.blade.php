@extends('layouts.guest')

@section('title', 'Registrácia — Sellwinar')

@section('content')
    <h1 class="font-heading text-2xl font-bold text-ink-800 dark:text-white mb-2">Vytvoriť účet</h1>
    <p class="text-ink-500 dark:text-ink-400 text-sm mb-6">14 dní zadarmo, žiadna kreditná karta</p>

    @if($errors->any())
        <div class="mb-4 bg-danger-50 dark:bg-danger-700/20 border-l-4 border-danger-500 rounded-lg p-3">
            @foreach($errors->all() as $error)
                <p class="text-sm text-danger-700 dark:text-danger-400">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Meno</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                   placeholder="Vaše meno">
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                   placeholder="vas@email.sk">
        </div>

        <div class="mb-4">
            <label for="company" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Názov firmy / projektu</label>
            <input type="text" id="company" name="company" value="{{ old('company') }}" required
                   class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                   placeholder="Moja firma s.r.o.">
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Heslo</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                   placeholder="Minimálne 8 znakov">
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Potvrdiť heslo</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm text-ink-800 dark:text-white placeholder-ink-400 focus:border-violet-600 ring-violet-focus transition-fast"
                   placeholder="Zopakujte heslo">
        </div>

        <button type="submit"
                class="w-full py-2.5 px-6 bg-violet-600 hover:bg-violet-500 active:bg-violet-700 text-white font-semibold text-sm rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            Vytvoriť účet zadarmo
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-500 dark:text-ink-400">
        Už máte účet?
        <a href="{{ route('login') }}" class="text-violet-600 hover:text-violet-500 font-medium">Prihláste sa</a>
    </p>
@endsection
