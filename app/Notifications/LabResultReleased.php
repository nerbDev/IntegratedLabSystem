<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class LabResultReleased extends Notification implements ShouldQueue
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
        return [
            'subject' => 'Your Lab Result is Now Available',
            'html' => view('emails.result-released', [
                'appointment' => $this->appointment,
            ])->render(),
        ];
    }
}