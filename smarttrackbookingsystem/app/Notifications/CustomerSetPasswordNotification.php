<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerSetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token, public $business) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('password.set', [
            'user' => $notifiable->id,
            'token' => $this->token,
        ]);

        return (new MailMessage)
            ->subject('Set your password - ' . $this->business->name)
            ->greeting('Hi ' . $notifiable->name)
            ->line('Your customer account has been created.')
            ->action('Set Password', $url)
            ->line('If you did not request this, ignore this email.');
    }
}