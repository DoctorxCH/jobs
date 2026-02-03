<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyVerificationCodeNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('365jobs – Company verification code')
            ->line('Dein Verifizierungscode lautet:')
            ->line($this->code)
            ->line('Der Code ist 10 Minuten gültig.');
    }
}
