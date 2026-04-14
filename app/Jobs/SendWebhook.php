<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [0, 300, 1800]; // immediate, 5 min, 30 min

    public function __construct(
        public Webhook $webhook,
        public string $eventType,
        public array $payload,
    ) {}

    public function handle(): void
    {
        $attempt = $this->attempts();

        $signature = hash_hmac('sha256', json_encode($this->payload), $this->webhook->secret);

        $log = WebhookLog::create([
            'webhook_id' => $this->webhook->id,
            'tenant_id' => $this->webhook->tenant_id,
            'event_type' => $this->eventType,
            'payload' => $this->payload,
            'attempt' => $attempt,
            'status' => 'pending',
        ]);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Sellwinar-Signature' => $signature,
                    'X-Sellwinar-Event' => $this->eventType,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->webhook->url, $this->payload);

            $log->update([
                'response_code' => $response->status(),
                'response_body' => substr($response->body(), 0, 1000),
                'status' => $response->successful() ? 'success' : 'failed',
            ]);

            $this->webhook->update(['last_triggered_at' => now()]);

            if (!$response->successful() && $attempt < 3) {
                $this->release($this->backoff[$attempt - 1] ?? 1800);
            }
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'response_body' => $e->getMessage(),
            ]);

            if ($attempt < 3) {
                $this->release($this->backoff[$attempt - 1] ?? 1800);
            }
        }
    }
}
