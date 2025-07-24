<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $minutesBefore;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Appointment  $appointment
     * @param  int  $minutesBefore
     * @return void
     */
    public function __construct(Appointment $appointment, int $minutesBefore = 30)
    {
        $this->appointment = $appointment;
        $this->minutesBefore = $minutesBefore;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $startTime = $this->appointment->start_date->format('g:i A');
        $startDate = $this->appointment->start_date->format('l, F j, Y');
        $timeUntil = $this->minutesBefore > 60 
            ? floor($this->minutesBefore / 60) . ' hour(s)' 
            : $this->minutesBefore . ' minutes';

        return (new MailMessage)
            ->subject('Reminder: Upcoming Appointment - ' . $this->appointment->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that you have an appointment coming up in ' . $timeUntil . '.')
            ->line('**' . $this->appointment->title . '**')
            ->line('**Date:** ' . $startDate)
            ->line('**Time:** ' . $startTime)
            ->line('**Location:** ' . ($this->appointment->location ?: 'Not specified'))
            ->line('**Description:** ' . $this->appointment->description)
            ->action('View Appointment', url('/appointments/' . $this->appointment->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'title' => $this->appointment->title,
            'start_date' => $this->appointment->start_date->toIso8601String(),
            'minutes_before' => $this->minutesBefore,
            'message' => 'You have an appointment "' . $this->appointment->title . '" in ' . $this->minutesBefore . ' minutes.',
        ];
    }
}