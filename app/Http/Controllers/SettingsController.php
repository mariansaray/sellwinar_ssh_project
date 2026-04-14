<?php

namespace App\Http\Controllers;

use App\Models\EmailConfig;
use App\Models\IncomingWebhookToken;
use App\Models\SmsConfig;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant;
        $emailConfig = EmailConfig::first();
        $smsConfig = SmsConfig::first();
        $webhooks = Webhook::orderByDesc('created_at')->get();
        $incomingToken = IncomingWebhookToken::where('is_active', true)->first();
        $apiKey = $user->api_key;

        return view('settings.index', compact('user', 'tenant', 'emailConfig', 'smsConfig', 'webhooks', 'incomingToken', 'apiKey'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
        ]);

        auth()->user()->update($validated);
        return back()->with('success', 'Profil bol aktualizovaný.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        auth()->user()->update(['password' => $request->password]);
        return back()->with('success', 'Heslo bolo zmenené.');
    }

    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => ['required', 'string', 'max:255'],
            'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'smtp_user' => ['required', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
            'from_email' => ['required', 'email', 'max:255'],
            'reply_to' => ['nullable', 'email', 'max:255'],
        ]);

        $config = EmailConfig::firstOrNew(['tenant_id' => app('current_tenant_id')]);
        $config->fill([
            'smtp_host' => $validated['smtp_host'],
            'smtp_port' => $validated['smtp_port'],
            'smtp_user' => $validated['smtp_user'],
            'from_name' => $validated['from_name'],
            'from_email' => $validated['from_email'],
            'reply_to' => $validated['reply_to'],
        ]);

        if (!empty($validated['smtp_password'])) {
            $config->smtp_pass_encrypted = Crypt::encryptString($validated['smtp_password']);
        }

        $config->tenant_id = app('current_tenant_id');
        $config->save();

        return back()->with('success', 'SMTP nastavenia boli uložené.');
    }

    public function testSmtp()
    {
        $config = EmailConfig::first();
        if (!$config || !$config->smtp_host) {
            return back()->with('error', 'Najprv nastavte SMTP údaje.');
        }

        try {
            Mail::raw('Toto je testovací email zo Sellwinar.', function ($message) use ($config) {
                $message->to(auth()->user()->email)
                    ->subject('Sellwinar — Test SMTP');
            });
            $config->update(['is_verified' => true]);
            return back()->with('success', 'Testovací email bol odoslaný na ' . auth()->user()->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Chyba pri odosielaní: ' . $e->getMessage());
        }
    }

    public function updateTwilio(Request $request)
    {
        $validated = $request->validate([
            'twilio_sid' => ['required', 'string', 'max:255'],
            'twilio_token' => ['nullable', 'string', 'max:255'],
            'twilio_phone' => ['required', 'string', 'max:20'],
        ]);

        $config = SmsConfig::firstOrNew(['tenant_id' => app('current_tenant_id')]);
        $config->fill([
            'twilio_sid' => $validated['twilio_sid'],
            'twilio_phone' => $validated['twilio_phone'],
            'is_active' => true,
        ]);

        if (!empty($validated['twilio_token'])) {
            $config->twilio_token_encrypted = Crypt::encryptString($validated['twilio_token']);
        }

        $config->tenant_id = app('current_tenant_id');
        $config->save();

        return back()->with('success', 'Twilio nastavenia boli uložené.');
    }

    public function regenerateApiKey()
    {
        auth()->user()->update(['api_key' => Str::random(64)]);
        return back()->with('success', 'API kľúč bol obnovený.');
    }

    // Webhooks
    public function storeWebhook(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:500'],
            'event_types' => ['required', 'array', 'min:1'],
            'event_types.*' => ['in:registration.created,registration.attended,registration.missed,registration.cta_clicked'],
        ]);

        Webhook::create([
            'tenant_id' => app('current_tenant_id'),
            'url' => $validated['url'],
            'event_types' => $validated['event_types'],
            'secret' => Str::random(64),
            'is_active' => true,
        ]);

        return back()->with('success', 'Webhook bol vytvorený.');
    }

    public function destroyWebhook(Webhook $webhook)
    {
        $webhook->delete();
        return back()->with('success', 'Webhook bol zmazaný.');
    }
}
