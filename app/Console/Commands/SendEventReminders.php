<?php

namespace App\Console\Commands;

use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all notification to all event attendees that event starts soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = \App\Models\Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])
            ->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        $this->info("Found {$eventCount} {$eventLabel} starting within the next 24 hours.");

        $events->each(fn($event) => $event->attendees->each(
            //fn ($attendee) => $this->info("Notifying {$attendee->user->email} about event '{$event->name}' starting at {$event->start_time}.")
            fn($attendee) => $attendee->user->notify(
                new EventReminderNotification($event)
            )
        ));

        $this->info('Reminder notification sent successfully!');
    }
}
