<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EmailTemplateController;
use App\Models\Webinar;
use App\Models\WebinarSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WebinarController extends Controller
{
    public function index(Request $request)
    {
        $query = Webinar::withCount('registrants')->latest();

        $type = $request->get('type') ?: $request->route()->defaults['type'] ?? null;
        if ($type) {
            $query->where('type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $webinars = $query->paginate(20)->withQueryString();

        return view('webinars.index', compact('webinars'));
    }

    public function create()
    {
        return view('webinars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:evergreen,smart_video'],
            'slug' => ['nullable', 'string', 'max:255'],
            'video_source' => ['nullable', 'in:youtube,vimeo,custom'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'status' => ['in:draft,active,paused,archived'],
        ]);

        $tenantId = app('current_tenant_id');
        $slug = $validated['slug'] ?: Str::slug($validated['name']);

        // Ensure slug is unique within tenant
        $baseSlug = $slug;
        $counter = 1;
        while (Webinar::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $webinar = Webinar::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'slug' => $slug,
            'video_source' => $validated['video_source'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'status' => $validated['status'] ?? 'draft',
            'player_config' => $this->defaultPlayerConfig(),
            'cta_config' => $this->defaultCtaConfig(),
            'registration_page_config' => $this->defaultRegistrationPageConfig($validated['name']),
            'thankyou_page_config' => $this->defaultThankyouPageConfig(),
        ]);

        // Create default schedule for evergreen
        if ($webinar->isEvergreen()) {
            WebinarSchedule::create([
                'webinar_id' => $webinar->id,
                'tenant_id' => $tenantId,
                'schedule_type' => 'jit',
                'jit_delay_minutes' => 15,
                'timezone' => 'Europe/Bratislava',
            ]);
        }

        // Create default email templates
        EmailTemplateController::createDefaults($webinar);

        return redirect()->route('dashboard.webinars.edit', $webinar)
            ->with('success', 'Webinár bol vytvorený.');
    }

    public function show(Webinar $webinar)
    {
        return redirect()->route('dashboard.webinars.edit', $webinar);
    }

    public function edit(Webinar $webinar)
    {
        $webinar->load(['schedule', 'chatConfig', 'emailTemplates', 'smsTemplates', 'trackingPixels', 'purchaseAlerts']);

        return view('webinars.edit', compact('webinar'));
    }

    public function update(Request $request, Webinar $webinar)
    {
        $tab = $request->get('tab', 'info');

        switch ($tab) {
            case 'info':
                $this->updateInfo($request, $webinar);
                break;
            case 'video':
                $this->updateVideo($request, $webinar);
                break;
            case 'player':
                $this->updatePlayer($request, $webinar);
                break;
            case 'schedule':
                $this->updateSchedule($request, $webinar);
                break;
            case 'registration':
                $this->updateRegistrationPage($request, $webinar);
                break;
            case 'thankyou':
                $this->updateThankyouPage($request, $webinar);
                break;
            case 'cta':
                $this->updateCta($request, $webinar);
                break;
        }

        return back()->with('success', 'Zmeny boli uložené.');
    }

    public function destroy(Webinar $webinar)
    {
        $webinar->delete();
        return redirect()->route('dashboard.webinars.index')->with('success', 'Webinár bol zmazaný.');
    }

    public function duplicate(Webinar $webinar)
    {
        $new = $webinar->replicate();
        $new->name = $webinar->name . ' (kópia)';
        $new->slug = $webinar->slug . '-copy-' . Str::random(4);
        $new->status = 'draft';
        $new->save();

        if ($webinar->schedule) {
            $schedule = $webinar->schedule->replicate();
            $schedule->webinar_id = $new->id;
            $schedule->save();
        }

        return redirect()->route('dashboard.webinars.edit', $new)
            ->with('success', 'Webinár bol duplikovaný.');
    }

    public function toggleStatus(Webinar $webinar)
    {
        $newStatus = $webinar->status === 'active' ? 'paused' : 'active';
        $webinar->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'aktivovaný' : 'pozastavený';
        return back()->with('success', "Webinár bol {$label}.");
    }

    // ---- Private helpers ----

    private function updateInfo(Request $request, Webinar $webinar): void
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'status' => ['in:draft,active,paused,archived'],
        ]);

        $webinar->update($validated);
    }

    private function updateVideo(Request $request, Webinar $webinar): void
    {
        $validated = $request->validate([
            'video_source' => ['required', 'in:youtube,vimeo,custom'],
            'video_url' => ['required', 'url', 'max:500'],
            'video_duration_seconds' => ['nullable', 'integer', 'min:0'],
            'thumbnail_url' => ['nullable', 'url', 'max:500'],
        ]);

        $webinar->update($validated);
    }

    private function updatePlayer(Request $request, Webinar $webinar): void
    {
        $config = $request->validate([
            'primaryColor' => ['nullable', 'string', 'max:7'],
            'showPlayPause' => ['boolean'],
            'showProgress' => ['boolean'],
            'allowSeeking' => ['boolean'],
            'fakeProgressBar' => ['boolean'],
            'fakeDurationSeconds' => ['nullable', 'integer', 'min:0'],
            'showVolume' => ['boolean'],
            'showFullscreen' => ['boolean'],
            'showSpeed' => ['boolean'],
            'autoplay' => ['boolean'],
            'startMuted' => ['boolean'],
        ]);

        $webinar->update(['player_config' => array_merge($webinar->player_config ?? [], $config)]);
    }

    private function updateSchedule(Request $request, Webinar $webinar): void
    {
        $validated = $request->validate([
            'schedule_type' => ['required', 'in:jit,fixed,interval'],
            'jit_delay_minutes' => ['nullable', 'integer', 'min:1'],
            'interval_hours' => ['nullable', 'integer', 'min:1'],
            'fixed_times' => ['nullable', 'array'],
            'timezone' => ['required', 'string'],
            'hide_night_times' => ['boolean'],
        ]);

        $webinar->schedule()->updateOrCreate(
            ['webinar_id' => $webinar->id],
            array_merge($validated, ['tenant_id' => $webinar->tenant_id])
        );
    }

    private function updateRegistrationPage(Request $request, Webinar $webinar): void
    {
        $config = $request->validate([
            'template' => ['in:1,2,3,4,5'],
            'headline' => ['nullable', 'string', 'max:500'],
            'subheadline' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'background_color' => ['nullable', 'string', 'max:7'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'show_social_proof' => ['boolean'],
            'show_countdown' => ['boolean'],
            'require_phone' => ['boolean'],
            'custom_css' => ['nullable', 'string'],
        ]);

        $webinar->update(['registration_page_config' => array_merge($webinar->registration_page_config ?? [], $config)]);
    }

    private function updateThankyouPage(Request $request, Webinar $webinar): void
    {
        $config = $request->validate([
            'headline' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string'],
            'show_countdown' => ['boolean'],
            'show_calendar_buttons' => ['boolean'],
            'interim_cta_text' => ['nullable', 'string', 'max:255'],
            'interim_cta_url' => ['nullable', 'url', 'max:500'],
        ]);

        $webinar->update(['thankyou_page_config' => array_merge($webinar->thankyou_page_config ?? [], $config)]);
    }

    private function updateCta(Request $request, Webinar $webinar): void
    {
        $config = $request->validate([
            'text' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:500'],
            'show_at_seconds' => ['nullable', 'integer', 'min:0'],
            'hide_at_seconds' => ['nullable', 'integer', 'min:0'],
            'button_color' => ['nullable', 'string', 'max:7'],
            'text_color' => ['nullable', 'string', 'max:7'],
            'sticky_on_mobile' => ['boolean'],
        ]);

        $webinar->update(['cta_config' => array_merge($webinar->cta_config ?? [], $config)]);
    }

    private function defaultPlayerConfig(): array
    {
        return [
            'primaryColor' => '#6C3AED',
            'backgroundColor' => '#000000',
            'showPlayPause' => true,
            'showProgress' => true,
            'allowSeeking' => false,
            'fakeProgressBar' => false,
            'fakeDurationSeconds' => null,
            'showVolume' => true,
            'showFullscreen' => true,
            'showSpeed' => false,
            'autoplay' => true,
            'startMuted' => true,
            'thumbnailUrl' => null,
        ];
    }

    private function defaultCtaConfig(): array
    {
        return [
            'text' => 'Chcem sa prihlásiť',
            'url' => '',
            'show_at_seconds' => 1800,
            'hide_at_seconds' => null,
            'button_color' => '#6C3AED',
            'text_color' => '#FFFFFF',
            'sticky_on_mobile' => true,
        ];
    }

    private function defaultRegistrationPageConfig(string $name): array
    {
        return [
            'template' => '1',
            'headline' => $name,
            'subheadline' => '',
            'description' => '',
            'primary_color' => '#6C3AED',
            'background_color' => '#FFFFFF',
            'cta_text' => 'Registrovať sa zadarmo',
            'show_social_proof' => true,
            'show_countdown' => true,
            'require_phone' => false,
            'custom_css' => '',
        ];
    }

    private function defaultThankyouPageConfig(): array
    {
        return [
            'headline' => 'Ďakujeme za registráciu!',
            'message' => 'Pošleme vám pripomienku pred začiatkom webinára.',
            'show_countdown' => true,
            'show_calendar_buttons' => true,
            'interim_cta_text' => '',
            'interim_cta_url' => '',
        ];
    }
}
