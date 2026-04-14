<?php

namespace App\Services;

use App\Jobs\SendWebhook;
use App\Models\Webhook;

class WebhookService
{
    /**
     * Dispatch webhooks for a given event
     */
    public static function dispatch(int $tenantId, string $eventType, array $payload): void
    {
        $webhooks = Webhook::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $eventTypes = $webhook->event_types ?? [];
            if (in_array($eventType, $eventTypes) || in_array('*', $eventTypes)) {
                SendWebhook::dispatch($webhook, $eventType, array_merge($payload, [
                    'event' => $eventType,
                    'timestamp' => now()->toIso8601String(),
                ]));
            }
        }
    }
}
