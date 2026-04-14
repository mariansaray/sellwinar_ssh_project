<?php

namespace App\Http\Controllers;

use App\Models\PurchaseAlert;
use App\Models\Webinar;
use Illuminate\Http\Request;

class PurchaseAlertController extends Controller
{
    public function index(Webinar $webinar)
    {
        $alerts = $webinar->purchaseAlerts()->orderBy('display_at_seconds')->get();
        return view('webinars.alerts.index', compact('webinar', 'alerts'));
    }

    public function store(Request $request, Webinar $webinar)
    {
        $validated = $request->validate([
            'buyer_name' => ['required', 'string', 'max:100'],
            'product_name' => ['required', 'string', 'max:255'],
            'display_at_seconds' => ['required', 'integer', 'min:0'],
        ]);

        $webinar->purchaseAlerts()->create(array_merge($validated, [
            'tenant_id' => $webinar->tenant_id,
            'sort_order' => PurchaseAlert::withoutGlobalScopes()->where('webinar_id', $webinar->id)->max('sort_order') + 1,
        ]));

        return back()->with('success', 'Alert bol pridaný.');
    }

    public function destroy(Webinar $webinar, PurchaseAlert $alert)
    {
        $alert->delete();
        return back()->with('success', 'Alert bol zmazaný.');
    }

    public function import(Request $request, Webinar $webinar)
    {
        $request->validate(['csv_data' => ['required', 'string']]);

        $lines = array_filter(explode("\n", $request->csv_data));
        $count = 0;

        foreach ($lines as $line) {
            $parts = str_getcsv(trim($line));
            if (count($parts) >= 3) {
                $webinar->purchaseAlerts()->create([
                    'tenant_id' => $webinar->tenant_id,
                    'buyer_name' => trim($parts[0]),
                    'product_name' => trim($parts[1]),
                    'display_at_seconds' => (int) trim($parts[2]),
                    'sort_order' => $count,
                ]);
                $count++;
            }
        }

        return back()->with('success', "{$count} alertov bolo importovaných.");
    }
}
