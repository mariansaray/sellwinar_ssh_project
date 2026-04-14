@extends('layouts.dashboard')

@section('title', 'Nastavenia — Sellwinar')

@section('breadcrumbs')
    <span class="text-ink-800 dark:text-white font-medium">Nastavenia</span>
@endsection

@section('sidebar')
    @include('partials.sidebar-user')
@endsection

@section('content')
<div x-data="{ tab: '{{ request('tab', 'profile') }}' }">
    <h1 class="font-heading text-3xl font-bold text-ink-800 dark:text-white mb-6">Nastavenia</h1>

    {{-- Tab nav --}}
    <div class="border-b border-ink-200 dark:border-ink-600 mb-6">
        <nav class="flex gap-0">
            @foreach(['profile' => 'Profil', 'smtp' => 'SMTP / E-mail', 'twilio' => 'Twilio / SMS', 'api' => 'API kľúče', 'webhooks' => 'Webhooky'] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'border-violet-600 text-violet-600 font-semibold' : 'border-transparent text-ink-500 dark:text-ink-400 hover:text-ink-700'"
                    class="px-4 py-3 text-sm border-b-2 transition-fast whitespace-nowrap">{{ $label }}</button>
            @endforeach
        </nav>
    </div>

    {{-- Profil --}}
    <div x-show="tab === 'profile'" x-cloak class="max-w-2xl space-y-6">
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Osobné údaje</h3>
            <form method="POST" action="{{ route('dashboard.settings.profile') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Meno</label>
                    <input type="text" name="name" value="{{ $user->name }}" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">E-mail</label>
                    <input type="email" name="email" value="{{ $user->email }}" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Uložiť</button>
            </form>
        </div>

        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Zmeniť heslo</h3>
            <form method="POST" action="{{ route('dashboard.settings.password') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Aktuálne heslo</label>
                    <input type="password" name="current_password" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Nové heslo</label>
                    <input type="password" name="password" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Potvrdiť nové heslo</label>
                    <input type="password" name="password_confirmation" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Zmeniť heslo</button>
            </form>
        </div>
    </div>

    {{-- SMTP --}}
    <div x-show="tab === 'smtp'" x-cloak class="max-w-2xl">
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white">SMTP konfigurácia</h3>
                @if($emailConfig && $emailConfig->is_verified)
                    <span class="inline-flex px-2.5 py-1 bg-success-50 text-success-700 text-xs font-medium rounded-full">Overené</span>
                @endif
            </div>
            <form method="POST" action="{{ route('dashboard.settings.smtp') }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">SMTP host</label>
                        <input type="text" name="smtp_host" value="{{ $emailConfig->smtp_host ?? '' }}" required placeholder="smtp.gmail.com" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Port</label>
                        <input type="number" name="smtp_port" value="{{ $emailConfig->smtp_port ?? 587 }}" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Používateľ</label>
                        <input type="text" name="smtp_user" value="{{ $emailConfig->smtp_user ?? '' }}" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Heslo</label>
                        <input type="password" name="smtp_password" placeholder="{{ $emailConfig && $emailConfig->smtp_pass_encrypted ? '••••••••' : '' }}" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Meno odosielateľa</label>
                        <input type="text" name="from_name" value="{{ $emailConfig->from_name ?? '' }}" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">E-mail odosielateľa</label>
                        <input type="email" name="from_email" value="{{ $emailConfig->from_email ?? '' }}" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Reply-to <span class="text-ink-400 font-normal">(voliteľné)</span></label>
                    <input type="email" name="reply_to" value="{{ $emailConfig->reply_to ?? '' }}" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Uložiť</button>
                    <form method="POST" action="{{ route('dashboard.settings.smtp.test') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 border border-ink-200 dark:border-ink-600 text-sm text-ink-600 dark:text-ink-400 rounded-lg hover:bg-ink-50 dark:hover:bg-ink-600 transition-fast">Odoslať testovací email</button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    {{-- Twilio --}}
    <div x-show="tab === 'twilio'" x-cloak class="max-w-2xl">
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Twilio (SMS)</h3>
            <form method="POST" action="{{ route('dashboard.settings.twilio') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Account SID</label>
                    <input type="text" name="twilio_sid" value="{{ $smsConfig->twilio_sid ?? '' }}" required class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Auth Token</label>
                    <input type="password" name="twilio_token" placeholder="{{ $smsConfig && $smsConfig->twilio_token_encrypted ? '••••••••' : '' }}" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">Telefónne číslo (Twilio)</label>
                    <input type="text" name="twilio_phone" value="{{ $smsConfig->twilio_phone ?? '' }}" required placeholder="+1234567890" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Uložiť</button>
            </form>
        </div>
    </div>

    {{-- API --}}
    <div x-show="tab === 'api'" x-cloak class="max-w-2xl">
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">API kľúč</h3>
            <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">Použite tento kľúč pre autentifikáciu API requestov.</p>
            <div class="flex items-center gap-3 mb-4">
                <code class="flex-1 px-4 py-3 bg-ink-50 dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-lg text-sm font-mono text-ink-800 dark:text-ink-200 break-all">{{ $apiKey ?? 'Žiadny API kľúč' }}</code>
            </div>
            <form method="POST" action="{{ route('dashboard.settings.api-key.regenerate') }}">
                @csrf
                <button type="submit" onclick="return confirm('Starý kľúč prestane fungovať. Pokračovať?')" class="px-6 py-2.5 bg-warning-500 hover:bg-warning-600 text-white text-sm font-semibold rounded-lg transition-fast">Vygenerovať nový kľúč</button>
            </form>
        </div>
    </div>

    {{-- Webhooks --}}
    <div x-show="tab === 'webhooks'" x-cloak class="max-w-3xl">
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl p-6 mb-6">
            <h3 class="font-heading text-lg font-semibold text-ink-800 dark:text-white mb-4">Nový webhook</h3>
            <form method="POST" action="{{ route('dashboard.settings.webhooks.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-1.5">URL</label>
                    <input type="url" name="url" required placeholder="https://example.com/webhook" class="w-full px-3.5 py-2.5 bg-white dark:bg-ink-800 border border-ink-200 dark:border-ink-600 rounded-md text-sm focus:border-violet-600 ring-violet-focus transition-fast">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-800 dark:text-ink-100 mb-2">Eventy</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['registration.created' => 'Nová registrácia', 'registration.attended' => 'Účasť', 'registration.missed' => 'Zmeškaný', 'registration.cta_clicked' => 'CTA klik'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="event_types[]" value="{{ $val }}" checked class="w-4 h-4 rounded border-ink-300 text-violet-600">
                            <span class="text-sm text-ink-700 dark:text-ink-300">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-lg transition-fast">Vytvoriť webhook</button>
            </form>
        </div>

        @if($webhooks->isNotEmpty())
        <div class="bg-white dark:bg-ink-700 border border-ink-200 dark:border-ink-600 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead><tr class="bg-ink-50 dark:bg-ink-800">
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">URL</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Eventy</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase text-ink-500">Stav</th>
                    <th class="px-4 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-ink-100 dark:divide-ink-600">
                    @foreach($webhooks as $wh)
                    <tr class="hover:bg-violet-50 dark:hover:bg-ink-600 transition-fast">
                        <td class="px-4 py-3 text-sm font-mono text-ink-800 dark:text-white break-all max-w-[300px]">{{ $wh->url }}</td>
                        <td class="px-4 py-3 text-xs text-ink-500">{{ implode(', ', $wh->event_types ?? []) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $wh->is_active ? 'bg-success-50 text-success-700' : 'bg-ink-100 text-ink-400' }}">
                                {{ $wh->is_active ? 'Aktívny' : 'Neaktívny' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('dashboard.settings.webhooks.destroy', $wh) }}" onsubmit="return confirm('Zmazať webhook?')">
                                @csrf @method('DELETE')
                                <button class="p-1 text-ink-400 hover:text-danger-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
