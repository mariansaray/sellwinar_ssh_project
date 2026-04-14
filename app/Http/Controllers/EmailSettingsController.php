<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateGlobal;
use App\Models\Webinar;
use Illuminate\Http\Request;

class EmailSettingsController extends Controller
{
    /**
     * Global email templates library
     */
    public function index()
    {
        $templates = EmailTemplateGlobal::orderBy('trigger_type')->orderBy('name')->get();
        $grouped = $templates->groupBy('trigger_type');

        $triggerLabels = [
            'registration_confirmed' => 'Potvrdenie registrácie',
            'reminder_24h' => 'Pripomienka 24h pred',
            'reminder_1h' => 'Pripomienka 1h pred',
            'reminder_15m' => 'Pripomienka 15 min pred',
            'reminder_5m' => 'Pripomienka 5 min pred',
            'missed' => 'Zmeškaný webinár',
            'replay' => 'Replay odkaz',
        ];

        return view('email-templates.index', compact('templates', 'grouped', 'triggerLabels'));
    }

    public function create()
    {
        $triggerTypes = [
            'registration_confirmed' => 'Potvrdenie registrácie',
            'reminder_24h' => 'Pripomienka 24h pred',
            'reminder_1h' => 'Pripomienka 1h pred',
            'reminder_15m' => 'Pripomienka 15 min pred',
            'reminder_5m' => 'Pripomienka 5 min pred',
            'missed' => 'Zmeškaný webinár',
            'replay' => 'Replay odkaz',
        ];

        return view('email-templates.create', compact('triggerTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'trigger_type' => ['required', 'string'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'delay_minutes' => ['required', 'integer'],
        ]);

        EmailTemplateGlobal::create($validated);

        return redirect()->route('dashboard.email-templates.index')
            ->with('success', 'Šablóna bola vytvorená.');
    }

    public function edit(EmailTemplateGlobal $emailTemplate)
    {
        $triggerTypes = [
            'registration_confirmed' => 'Potvrdenie registrácie',
            'reminder_24h' => 'Pripomienka 24h pred',
            'reminder_1h' => 'Pripomienka 1h pred',
            'reminder_15m' => 'Pripomienka 15 min pred',
            'reminder_5m' => 'Pripomienka 5 min pred',
            'missed' => 'Zmeškaný webinár',
            'replay' => 'Replay odkaz',
        ];

        return view('email-templates.edit', compact('emailTemplate', 'triggerTypes'));
    }

    public function update(Request $request, EmailTemplateGlobal $emailTemplate)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'trigger_type' => ['required', 'string'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'delay_minutes' => ['required', 'integer'],
        ]);

        $emailTemplate->update($validated);

        return redirect()->route('dashboard.email-templates.index')
            ->with('success', 'Šablóna bola aktualizovaná.');
    }

    public function destroy(EmailTemplateGlobal $emailTemplate)
    {
        if ($emailTemplate->is_default) {
            return back()->with('error', 'Predvolené šablóny nie je možné zmazať.');
        }

        $emailTemplate->delete();
        return back()->with('success', 'Šablóna bola zmazaná.');
    }

    /**
     * Apply a global template to a specific webinar
     */
    public function applyToWebinar(Request $request, EmailTemplateGlobal $emailTemplate)
    {
        $request->validate([
            'webinar_id' => ['required', 'exists:webinars,id'],
        ]);

        $webinar = Webinar::findOrFail($request->webinar_id);

        EmailTemplate::create([
            'tenant_id' => $webinar->tenant_id,
            'webinar_id' => $webinar->id,
            'trigger_type' => $emailTemplate->trigger_type,
            'subject' => $emailTemplate->subject,
            'body_html' => $emailTemplate->body_html,
            'delay_minutes' => $emailTemplate->delay_minutes,
            'is_active' => true,
        ]);

        return back()->with('success', "Šablóna \"{$emailTemplate->name}\" bola pridaná do webinára \"{$webinar->name}\".");
    }
}
