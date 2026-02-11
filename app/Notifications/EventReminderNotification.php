<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Event $event
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $eventDate = $this->event->date->format('F j, Y');
        $eventTime = $this->event->time;
        $eventLocation = $this->event->location;

        return (new MailMessage)
            ->subject('Reminder: Upcoming Event Tomorrow - ' . $this->event->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a friendly reminder that you have registered for an event happening tomorrow.')
            ->line('**Event Details:**')
            ->line('**Title:** ' . $this->event->title)
            ->line('**Date:** ' . $eventDate)
            ->line('**Time:** ' . $eventTime)
            ->line('**Location:** ' . $eventLocation)
            ->line('**Description:**')
            ->line($this->event->description)
            ->action('View Event Details', route('events.show', $this->event))
            ->line('Thank you for your commitment to volunteering!')
            ->salutation('Best regards, Volunteer Management System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_date' => $this->event->date->toDateString(),
            'event_time' => $this->event->time,
            'event_location' => $this->event->location,
        ];
    }
}
