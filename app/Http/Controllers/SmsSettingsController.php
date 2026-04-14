<?php

namespace App\Http\Controllers;

use App\Models\SmsTemplate;
use App\Models\Webinar;

class SmsSettingsController extends Controller
{
    public function index()
    {
        $webinars = Webinar::withCount('smsTemplates')->get();
        $templates = SmsTemplate::with('webinar')->orderBy('webinar_id')->orderBy('trigger_type')->get();
        $grouped = $templates->groupBy('webinar_id');

        return view('sms-templates.index', compact('webinars', 'grouped'));
    }
}
