<?php

namespace App\Notifications\Channels;

use App\Services\BrevoMailer;
use Illuminate\Notifications\Notification;

class BrevoChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $data = $notification->toBrevo($notifiable);

        (new BrevoMailer())->send(
            $notifiable->email,
            $notifiable->name ?? $notifiable->first_name,
            $data['subject'],
            $data['html']
        );
    }
}