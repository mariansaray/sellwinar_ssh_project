@extends('layouts.app')

@section('title', '404 — Stránka nenájdená')

@section('body')
<div class="min-h-screen flex items-center justify-center px-4 bg-[#FAFAFA] dark:bg-ink-900">
    <div class="text-center max-w-md">
        <p class="font-heading text-7xl font-bold text-violet-600 mb-4">404</p>
        <h1 class="font-heading text-2xl font-bold text-ink-800 dark:text-white mb-3">Stránka nenájdená</h1>
        <p class="text-ink-500 dark:text-ink-400 mb-8">Prepáčte, stránka ktorú hľadáte neexistuje alebo bola presunutá.</p>
        <a href="/" class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg shadow hover:shadow-violet transition-fast hover:-translate-y-px">
            Späť na úvodnú stránku
        </a>
    </div>
</div>
@endsection
