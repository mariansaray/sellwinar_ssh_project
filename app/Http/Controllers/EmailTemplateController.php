<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Webinar;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index(Webinar $webinar)
    {
        $templates = $webinar->emailTemplates()->orderBy('delay_minutes')->get();
        return view('webinars.emails.index', compact('webinar', 'templates'));
    }

    public function store(Request $request, Webinar $webinar)
    {
        $validated = $request->validate([
            'trigger_type' => ['required', 'string', 'in:registration_confirmed,reminder_24h,reminder_1h,reminder_15m,reminder_5m,missed,replay'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'is_active' => ['boolean'],
            'delay_minutes' => ['required', 'integer'],
        ]);

        $webinar->emailTemplates()->create(array_merge($validated, [
            'tenant_id' => $webinar->tenant_id,
        ]));

        return back()->with('success', 'Šablóna bola vytvorená.');
    }

    public function update(Request $request, Webinar $webinar, EmailTemplate $template)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'is_active' => ['boolean'],
        ]);

        $template->update($validated);
        return back()->with('success', 'Šablóna bola aktualizovaná.');
    }

    public function destroy(Webinar $webinar, EmailTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Šablóna bola zmazaná.');
    }

    /**
     * Create default templates for a webinar
     */
    public static function createDefaults(Webinar $webinar): void
    {
        $defaults = [
            [
                'trigger_type' => 'registration_confirmed',
                'subject' => 'Potvrdenie registrácie — {{nazov_webinara}}',
                'body_html' => '<p>Dobrý deň {{meno}},</p><p>Ďakujeme za registráciu na webinár <strong>{{nazov_webinara}}</strong>.</p><p>Váš webinár začne <strong>{{datum_webinara}} o {{cas_webinara}}</strong>.</p><p><a href="{{link_na_webinar}}">Odkaz na webinár</a></p>',
                'delay_minutes' => 0,
                'is_active' => true,
            ],
            [
                'trigger_type' => 'reminder_1h',
                'subject' => 'Pripomienka: Webinár začne o 1 hodinu',
                'body_html' => '<p>Dobrý deň {{meno}},</p><p>Pripomíname vám, že webinár <strong>{{nazov_webinara}}</strong> začne o 1 hodinu.</p><p><a href="{{link_na_webinar}}">Pripojiť sa</a></p>',
                'delay_minutes' => -60,
                'is_active' => true,
            ],
            [
                'trigger_type' => 'reminder_15m',
                'subject' => 'Začíname o 15 minút!',
                'body_html' => '<p>{{meno}}, webinár <strong>{{nazov_webinara}}</strong> začne o 15 minút!</p><p><a href="{{link_na_webinar}}">Pripojiť sa teraz</a></p>',
                'delay_minutes' => -15,
                'is_active' => true,
            ],
            [
                'trigger_type' => 'missed',
                'subject' => 'Zmeškali ste webinár — {{nazov_webinara}}',
                'body_html' => '<p>Dobrý deň {{meno}},</p><p>Všimli sme si, že ste sa nezúčastnili webinára <strong>{{nazov_webinara}}</strong>.</p><p>Ak máte záujem, môžete sa zaregistrovať na ďalší termín.</p>',
                'delay_minutes' => 240,
                'is_active' => false,
            ],
        ];

        foreach ($defaults as $tpl) {
            $webinar->emailTemplates()->create(array_merge($tpl, [
                'tenant_id' => $webinar->tenant_id,
            ]));
        }
    }
}
