@extends('layouts.guest')

@section('title', 'Vyberte si plán — Sellwinar')

@section('content')
    <h1 class="font-heading text-2xl font-bold text-ink-800 dark:text-white mb-2">Vyberte si plán</h1>
    <p class="text-ink-500 dark:text-ink-400 text-sm mb-6">
        Vaše skúšobné obdobie vypršalo. Vyberte si plán pre pokračovanie.
    </p>

    <div class="space-y-3">
        <div class="p-4 border border-ink-200 dark:border-ink-600 rounded-xl hover:border-violet-400 transition-fast">
            <div class="flex items-center justify-between mb-2">
                <span class="font-heading font-semibold text-ink-800 dark:text-white">Mesačný</span>
                <span class="font-heading font-bold text-violet-600">39 &euro;/mes</span>
            </div>
            <p class="text-xs text-ink-500">Neobmedzené webináre, smart videá a registrácie</p>
        </div>
        <div class="p-4 border-2 border-violet-600 rounded-xl bg-violet-50 dark:bg-violet-900/20">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="font-heading font-semibold text-ink-800 dark:text-white">Ročný</span>
                    <span class="px-2 py-0.5 bg-violet-600 text-white text-[10px] font-bold rounded-full">-17%</span>
                </div>
                <span class="font-heading font-bold text-violet-600">390 &euro;/rok</span>
            </div>
            <p class="text-xs text-ink-500">32,50 &euro;/mes — ušetríte 78 &euro; ročne</p>
        </div>
        <div class="p-4 border border-ink-200 dark:border-ink-600 rounded-xl hover:border-violet-400 transition-fast">
            <div class="flex items-center justify-between mb-2">
                <span class="font-heading font-semibold text-ink-800 dark:text-white">Lifetime</span>
                <span class="font-heading font-bold text-violet-600">1 170 &euro;</span>
            </div>
            <p class="text-xs text-ink-500">Jednorazová platba, prístup navždy</p>
        </div>
    </div>

    <p class="mt-6 text-xs text-ink-400 text-center">Platobná integrácia (Stripe) bude implementovaná vo Fáze 13.</p>
@endsection
