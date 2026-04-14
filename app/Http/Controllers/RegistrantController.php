<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\ChatMessageReal;
use App\Models\NotificationLog;
use App\Models\Registrant;
use App\Models\Webinar;
use Illuminate\Http\Request;

class RegistrantController extends Controller
{
    public function index(Request $request)
    {
        $query = Registrant::with('webinar')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($webinarId = $request->get('webinar_id')) {
            $query->where('webinar_id', $webinarId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $registrants = $query->paginate(50)->withQueryString();
        $webinars = Webinar::select('id', 'name')->get();

        return view('registrants.index', compact('registrants', 'webinars'));
    }

    public function show(Registrant $registrant)
    {
        $registrant->load('webinar');

        $notifications = NotificationLog::withoutGlobalScopes()
            ->where('registrant_id', $registrant->id)
            ->orderByDesc('created_at')
            ->get();

        $chatMessages = ChatMessageReal::withoutGlobalScopes()
            ->where('registrant_id', $registrant->id)
            ->orderBy('created_at')
            ->get();

        $events = AnalyticsEvent::withoutGlobalScopes()
            ->where('registrant_id', $registrant->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('registrants.show', compact('registrant', 'notifications', 'chatMessages', 'events'));
    }

    public function exportCsv(Request $request)
    {
        $query = Registrant::with('webinar');

        if ($webinarId = $request->get('webinar_id')) {
            $query->where('webinar_id', $webinarId);
        }

        $registrants = $query->orderBy('created_at')->get();
        $filename = 'registrants-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($registrants) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Meno', 'Priezvisko', 'Email', 'Telefón', 'Webinár', 'Stav', 'Dátum registrácie', 'UTM Source', 'UTM Medium', 'UTM Campaign']);

            foreach ($registrants as $r) {
                fputcsv($out, [
                    $r->first_name, $r->last_name, $r->email, $r->phone,
                    $r->webinar->name ?? '—', $r->status,
                    $r->created_at->format('Y-m-d H:i'),
                    $r->utm_source, $r->utm_medium, $r->utm_campaign,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
