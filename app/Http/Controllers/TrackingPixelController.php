<?php

namespace App\Http\Controllers;

use App\Models\TrackingPixel;
use App\Models\Webinar;
use Illuminate\Http\Request;

class TrackingPixelController extends Controller
{
    public function index(Webinar $webinar)
    {
        $pixels = $webinar->trackingPixels()->get();
        return view('webinars.tracking.index', compact('webinar', 'pixels'));
    }

    public function store(Request $request, Webinar $webinar)
    {
        $validated = $request->validate([
            'pixel_type' => ['required', 'in:facebook,ga4,google_ads,tiktok,custom'],
            'pixel_id' => ['nullable', 'string', 'max:255'],
            'page_placement' => ['nullable', 'array'],
            'page_placement.*' => ['in:registration,thankyou,webinar_room'],
            'custom_events' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $webinar->trackingPixels()->create(array_merge($validated, [
            'tenant_id' => $webinar->tenant_id,
            'custom_events' => $validated['custom_events'] ? json_decode($validated['custom_events'], true) : null,
        ]));

        return back()->with('success', 'Pixel bol pridaný.');
    }

    public function destroy(Webinar $webinar, TrackingPixel $pixel)
    {
        $pixel->delete();
        return back()->with('success', 'Pixel bol zmazaný.');
    }
}
