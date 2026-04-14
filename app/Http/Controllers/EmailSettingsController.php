<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Webinar;

class EmailSettingsController extends Controller
{
    public function index()
    {
        $webinars = Webinar::withCount('emailTemplates')->get();
        $templates = EmailTemplate::with('webinar')->orderBy('webinar_id')->orderBy('trigger_type')->get();
        $grouped = $templates->groupBy('webinar_id');

        return view('email-templates.index', compact('webinars', 'grouped'));
    }
}
