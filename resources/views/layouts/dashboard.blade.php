@extends('layouts.app')

@section('body')
<div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/50 dark:bg-black/70 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed z-40 inset-y-0 left-0 w-[260px] bg-ink-50 dark:bg-ink-800 border-r border-ink-200 dark:border-ink-600 transform transition-transform duration-200 lg:translate-x-0 lg:static lg:z-auto flex flex-col">
        {{-- Logo --}}
        <div class="h-14 flex items-center px-6 border-b border-ink-200 dark:border-ink-600">
            <a href="{{ route('dashboard.index') }}" class="flex items-center gap-1">
                <span class="font-heading text-xl font-bold text-violet-600">Sell</span>
                <span class="font-heading text-xl font-bold text-ink-800 dark:text-white">inar</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            @yield('sidebar')
        </nav>

        {{-- Sidebar footer --}}
        <div class="p-4 border-t border-ink-200 dark:border-ink-600">
            <div class="text-xs text-ink-400">{{ config('app.name') }} v1.0</div>
        </div>
    </aside>

    {{-- Main area --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Topbar --}}
        <header class="h-14 bg-white dark:bg-ink-800 border-b border-ink-200 dark:border-ink-600 sticky top-0 z-20 flex items-center justify-between px-4 lg:px-6">
            {{-- Mobile menu button --}}
            <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-700 transition-fast">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>

            {{-- Breadcrumbs --}}
            <div class="hidden lg:flex items-center gap-2 text-sm text-ink-500">
                @yield('breadcrumbs')
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                {{-- Dark mode toggle --}}
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-700 transition-fast" title="Prepnúť tmavý režim">
                    <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                    <i x-show="darkMode" data-lucide="sun" class="w-5 h-5"></i>
                </button>

                {{-- User menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 p-2 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-700 transition-fast">
                        <div class="w-8 h-8 rounded-full bg-violet-600 flex items-center justify-center text-white text-sm font-semibold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium">{{ Auth::user()->name ?? '' }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-ink-400"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl shadow-lg py-1 z-50">
                        <a href="{{ route('dashboard.settings.index') }}" class="block px-4 py-2 text-sm text-ink-600 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-600">Nastavenia</a>
                        @if(Auth::user()->isSuperAdmin())
                            <a href="{{ route('super-admin.dashboard') }}" class="block px-4 py-2 text-sm text-violet-600 hover:bg-ink-50 dark:hover:bg-ink-600">Super Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-danger-600 hover:bg-ink-50 dark:hover:bg-ink-600">Odhlásiť sa</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Impersonation banner --}}
        @if(session('impersonating'))
        <div class="bg-warning-50 dark:bg-warning-700/20 border-b border-warning-400 px-4 py-2 flex items-center justify-between">
            <span class="text-sm text-warning-700 dark:text-warning-400">
                <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                Ste prihlásený ako <strong>{{ Auth::user()->name }}</strong>
            </span>
            <a href="{{ route('super-admin.stop-impersonation') }}" class="text-sm font-medium text-warning-700 dark:text-warning-400 underline hover:no-underline">Vrátiť sa</a>
        </div>
        @endif

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mx-4 lg:mx-6 mt-4 bg-success-50 dark:bg-success-700/20 border-l-4 border-success-500 rounded-lg p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-success-600"></i>
                <span class="text-sm text-ink-800 dark:text-success-100">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-ink-400 hover:text-ink-600"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="mx-4 lg:mx-6 mt-4 bg-danger-50 dark:bg-danger-700/20 border-l-4 border-danger-500 rounded-lg p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-danger-600"></i>
                <span class="text-sm text-ink-800 dark:text-danger-100">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-ink-400 hover:text-ink-600"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-4 lg:p-6">
            <div class="max-w-[1280px] mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</div>
@endsection
