<?php

namespace App\Console\Commands;

use App\Models\EventRegistration;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to volunteers for events happening tomorrow';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting event reminder process...');

        // Get tomorrow's date
        $tomorrow = now()->addDay()->format('Y-m-d');

        // Find all approved registrations for events happening tomorrow
        $registrations = EventRegistration::with(['user', 'event'])
            ->whereHas('event', function ($query) use ($tomorrow) {
                $query->whereDate('date', $tomorrow);
            })
            ->where('status', 'approved')
            ->get();

        if ($registrations->isEmpty()) {
            $this->info('No events scheduled for tomorrow. No reminders to send.');
            Log::info('Event reminders: No events scheduled for tomorrow.');
            return Command::SUCCESS;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($registrations as $registration) {
            try {
                // Check if user has notification preferences enabled
                if ($registration->user->notification_pref === false) {
                    $this->warn("Skipping reminder for {$registration->user->email} - notifications disabled");
                    Log::info("Event reminder skipped for user {$registration->user->id}: notifications disabled");
                    continue;
                }

                // Send the notification
                $registration->user->notify(new EventReminderNotification($registration->event));
                
                $sentCount++;
                $this->line("Sent reminder to {$registration->user->name} ({$registration->user->email}) for event: {$registration->event->title}");
                Log::info("Event reminder sent to user {$registration->user->id} for event {$registration->event->id}");
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("Failed to send reminder to {$registration->user->email}: {$e->getMessage()}");
                Log::error("Failed to send event reminder to user {$registration->user->id}", [
                    'error' => $e->getMessage(),
                    'event_id' => $registration->event->id,
                ]);
            }
        }

        $this->info("Reminder process completed. Sent: {$sentCount}, Failed: {$failedCount}");
        Log::info("Event reminders completed", [
            'sent' => $sentCount,
            'failed' => $failedCount,
            'total_registrations' => $registrations->count(),
        ]);

        return Command::SUCCESS;
    }
}
