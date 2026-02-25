<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmployeeSetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // ✅ Your custom set-password route (recommended)
        $url = route('password.set', [
            'user'  => $notifiable->id,
            'token' => $this->token,
        ]);

       return (new MailMessage)
        ->subject('Set your password - ' . config('app.name'))
        ->markdown('emails.employee-set-password', [
            'user' => $notifiable,
            'url'  => $url,
        ]);
    }
}