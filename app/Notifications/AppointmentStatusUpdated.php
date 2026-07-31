<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected $appointment)
    {
    }

    public function via($notifiable): array
    {
        return ['brevo'];
    }

    public function toBrevo($notifiable): array
    {
        $status = ucfirst($this->appointment->status);

        return [
            'subject' => "Your Appointment has been {$status}",
            'html' => view('emails.appointment-status', [
                'appointment' => $this->appointment,
                'status' => $status,
            ])->render(),
        ];
    }
}