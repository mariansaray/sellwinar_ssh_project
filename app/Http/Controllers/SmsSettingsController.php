<?php

namespace App\Http\Controllers;

use App\Models\SmsTemplate;
use App\Models\SmsTemplateGlobal;
use App\Models\Webinar;
use Illuminate\Http\Request;

class SmsSettingsController extends Controller
{
    public function index()
    {
        $templates = SmsTemplateGlobal::orderBy('trigger_type')->orderBy('name')->get();
        $grouped = $templates->groupBy('trigger_type');

        $triggerLabels = [
            'registration_confirmed' => 'Potvrdenie registrácie',
            'reminder_1h' => 'Pripomienka 1h pred',
            'reminder_15m' => 'Pripomienka 15 min pred',
            'reminder_5m' => 'Pripomienka 5 min pred',
            'missed' => 'Zmeškaný webinár',
        ];

        return view('sms-templates.index', compact('templates', 'grouped', 'triggerLabels'));
    }

    public function create()
    {
        $triggerTypes = [
            'registration_confirmed' => 'Potvrdenie registrácie',
            'reminder_1h' => 'Pripomienka 1h pred',
            'reminder_15m' => 'Pripomienka 15 min pred',
            'reminder_5m' => 'Pripomienka 5 min pred',
            'missed' => 'Zmeškaný webinár',
        ];

        return view('sms-templates.create', compact('triggerTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'trigger_type' => ['required', 'string'],
            'message_text' => ['required', 'string', 'max:320'],
            'delay_minutes' => ['required', 'integer'],
        ]);

        SmsTemplateGlobal::create($validated);

        return redirect()->route('dashboard.sms-templates.index')
            ->with('success', 'SMS šablóna bola vytvorená.');
    }

    public function edit(SmsTemplateGlobal $smsTemplate)
    {
        $triggerTypes = [
            'registration_confirmed' => 'Potvrdenie registrácie',
            'reminder_1h' => 'Pripomienka 1h pred',
            'reminder_15m' => 'Pripomienka 15 min pred',
            'reminder_5m' => 'Pripomienka 5 min pred',
            'missed' => 'Zmeškaný webinár',
        ];

        return view('sms-templates.edit', compact('smsTemplate', 'triggerTypes'));
    }

    public function update(Request $request, SmsTemplateGlobal $smsTemplate)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'trigger_type' => ['required', 'string'],
            'message_text' => ['required', 'string', 'max:320'],
            'delay_minutes' => ['required', 'integer'],
        ]);

        $smsTemplate->update($validated);

        return redirect()->route('dashboard.sms-templates.index')
            ->with('success', 'SMS šablóna bola aktualizovaná.');
    }

    public function destroy(SmsTemplateGlobal $smsTemplate)
    {
        $smsTemplate->delete();
        return back()->with('success', 'SMS šablóna bola zmazaná.');
    }

    public function applyToWebinar(Request $request, SmsTemplateGlobal $smsTemplate)
    {
        $request->validate(['webinar_id' => ['required', 'exists:webinars,id']]);
        $webinar = Webinar::findOrFail($request->webinar_id);

        SmsTemplate::create([
            'tenant_id' => $webinar->tenant_id,
            'webinar_id' => $webinar->id,
            'trigger_type' => $smsTemplate->trigger_type,
            'message_text' => $smsTemplate->message_text,
            'delay_minutes' => $smsTemplate->delay_minutes,
            'is_active' => true,
        ]);

        return back()->with('success', "SMS šablóna bola pridaná do webinára \"{$webinar->name}\".");
    }
}
