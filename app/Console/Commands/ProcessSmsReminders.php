<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\Registrant;
use App\Models\SmsConfig;
use App\Models\SmsTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class ProcessSmsReminders extends Command
{
    protected $signature = 'sellwinar:process-sms';
    protected $description = 'Process and send scheduled SMS reminders via Twilio';

    public function handle(): int
    {
        $templates = SmsTemplate::withoutGlobalScopes()
            ->where('is_active', true)
            ->with('webinar')
            ->get();

        $sent = 0;

        foreach ($templates as $template) {
            $webinar = $template->webinar;
            if (!$webinar || $webinar->status !== 'active') continue;

            $smsConfig = SmsConfig::withoutGlobalScopes()
                ->where('tenant_id', $template->tenant_id)
                ->where('is_active', true)
                ->first();

            if (!$smsConfig || !$smsConfig->twilio_sid) continue;

            $registrants = $this->findEligible($template);

            foreach ($registrants as $registrant) {
                if (!$registrant->phone) continue;

                $alreadySent = NotificationLog::withoutGlobalScopes()
                    ->where('registrant_id', $registrant->id)
                    ->where('channel', 'sms')
                    ->where('template_id', $template->id)
                    ->exists();

                if ($alreadySent) continue;

                $this->sendSms($smsConfig, $template, $registrant, $webinar);
                $sent++;
            }
        }

        $this->info("Processed {$sent} SMS.");
        return self::SUCCESS;
    }

    private function findEligible(SmsTemplate $template)
    {
        $now = now();

        if ($template->trigger_type === 'registration_confirmed') {
            return Registrant::withoutGlobalScopes()
                ->where('webinar_id', $template->webinar_id)
                ->whereNotNull('phone')
                ->where('created_at', '>=', $now->copy()->subMinutes(2))
                ->get();
        }

        $minutesBefore = abs($template->delay_minutes);
        $targetTime = $now->copy()->addMinutes($minutesBefore);

        return Registrant::withoutGlobalScopes()
            ->where('webinar_id', $template->webinar_id)
            ->whereNotNull('phone')
            ->where('status', 'registered')
            ->whereBetween('scheduled_at', [
                $targetTime->copy()->subMinute(),
                $targetTime->copy()->addMinute(),
            ])
            ->get();
    }

    private function sendSms(SmsConfig $config, SmsTemplate $template, Registrant $registrant, $webinar): void
    {
        $message = $this->replacePlaceholders($template->message_text, $registrant, $webinar);

        $log = NotificationLog::withoutGlobalScopes()->create([
            'tenant_id' => $template->tenant_id,
            'registrant_id' => $registrant->id,
            'channel' => 'sms',
            'template_id' => $template->id,
            'status' => 'queued',
        ]);

        try {
            $token = Crypt::decryptString($config->twilio_token_encrypted);

            $response = Http::withBasicAuth($config->twilio_sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$config->twilio_sid}/Messages.json", [
                    'To' => $registrant->phone,
                    'From' => $config->twilio_phone,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $log->update(['status' => 'sent', 'sent_at' => now()]);
            } else {
                $log->update(['status' => 'failed', 'error_message' => $response->body()]);
            }
        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }

    private function replacePlaceholders(string $text, Registrant $registrant, $webinar): string
    {
        return str_replace(
            ['{{meno}}', '{{email}}', '{{nazov_webinara}}', '{{datum_webinara}}', '{{cas_webinara}}', '{{link_na_webinar}}'],
            [
                $registrant->first_name ?? 'Účastník',
                $registrant->email,
                $webinar->name,
                $registrant->scheduled_at?->format('d.m.Y') ?? '',
                $registrant->scheduled_at?->format('H:i') ?? '',
                route('public.watch', $registrant->access_token),
            ],
            $text
        );
    }
}
