<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use App\Models\NotificationLog;
use App\Models\Registrant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessEmailReminders extends Command
{
    protected $signature = 'sellwinar:process-emails';
    protected $description = 'Process and send scheduled email reminders';

    public function handle(): int
    {
        $templates = EmailTemplate::withoutGlobalScopes()
            ->where('is_active', true)
            ->with('webinar')
            ->get();

        $sent = 0;

        foreach ($templates as $template) {
            $webinar = $template->webinar;
            if (!$webinar || $webinar->status !== 'active') continue;

            // Find registrants who should receive this email NOW
            $registrants = $this->findEligibleRegistrants($template);

            foreach ($registrants as $registrant) {
                // Check if already sent
                $alreadySent = NotificationLog::withoutGlobalScopes()
                    ->where('registrant_id', $registrant->id)
                    ->where('channel', 'email')
                    ->where('template_id', $template->id)
                    ->exists();

                if ($alreadySent) continue;

                // Queue the email
                $this->sendEmail($template, $registrant, $webinar);
                $sent++;
            }
        }

        $this->info("Processed {$sent} emails.");
        return self::SUCCESS;
    }

    private function findEligibleRegistrants(EmailTemplate $template)
    {
        $now = now();

        // delay_minutes: negative = before scheduled_at, positive = after scheduled_at, 0 = immediately on registration
        if ($template->trigger_type === 'registration_confirmed') {
            // Send immediately after registration — find recent registrants (last 2 minutes)
            return Registrant::withoutGlobalScopes()
                ->where('webinar_id', $template->webinar_id)
                ->where('created_at', '>=', $now->copy()->subMinutes(2))
                ->get();
        }

        if ($template->trigger_type === 'missed') {
            // Send X minutes after scheduled_at for registrants who didn't attend
            $targetTime = $now->copy()->subMinutes($template->delay_minutes);
            return Registrant::withoutGlobalScopes()
                ->where('webinar_id', $template->webinar_id)
                ->where('status', 'registered') // not attended
                ->whereBetween('scheduled_at', [
                    $targetTime->copy()->subMinute(),
                    $targetTime,
                ])
                ->get();
        }

        // Reminder emails: delay_minutes is negative (e.g., -60 = 1h before)
        $minutesBefore = abs($template->delay_minutes);
        $targetTime = $now->copy()->addMinutes($minutesBefore);

        return Registrant::withoutGlobalScopes()
            ->where('webinar_id', $template->webinar_id)
            ->where('status', 'registered')
            ->whereBetween('scheduled_at', [
                $targetTime->copy()->subMinute(),
                $targetTime->copy()->addMinute(),
            ])
            ->get();
    }

    private function sendEmail(EmailTemplate $template, Registrant $registrant, $webinar): void
    {
        $body = $this->replacePlaceholders($template->body_html, $registrant, $webinar);
        $subject = $this->replacePlaceholders($template->subject, $registrant, $webinar);

        // Log first (as queued)
        $log = NotificationLog::withoutGlobalScopes()->create([
            'tenant_id' => $template->tenant_id,
            'registrant_id' => $registrant->id,
            'channel' => 'email',
            'template_id' => $template->id,
            'status' => 'queued',
        ]);

        try {
            Mail::html($body, function ($message) use ($registrant, $subject) {
                $message->to($registrant->email, $registrant->first_name)
                    ->subject($subject);
            });

            $log->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }

    private function replacePlaceholders(string $text, Registrant $registrant, $webinar): string
    {
        $replacements = [
            '{{meno}}' => $registrant->first_name ?? 'Účastník',
            '{{email}}' => $registrant->email,
            '{{nazov_webinara}}' => $webinar->name,
            '{{datum_webinara}}' => $registrant->scheduled_at?->format('d.m.Y') ?? '',
            '{{cas_webinara}}' => $registrant->scheduled_at?->format('H:i') ?? '',
            '{{link_na_webinar}}' => route('public.watch', $registrant->access_token),
            '{{link_na_zrusenie}}' => '#', // TODO: implement cancellation
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
