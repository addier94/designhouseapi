<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    public function toMail($notifiable)
    {
        $url = url(config('app.client_url').'/password/reset/'.$this->token).'?email='.urlencode($notifiable->email);
        return (new MailMessage)
                    ->line('Recibimos una solicitud de restablecimiento de contraseña para su cuenta')
                    ->action('Resetear contraseña', $url)
                    ->line('Si no solicitó un restablecimiento de contraseña, no se requiere otra acción.');
    }
}
