<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Sent to a SID-provisioned account. Carries a temporary signed activation
 * link valid for 24 hours (see ticket 01).
 */
class AccountActivationNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'account.activate',
            now()->addHours(24),
            ['user' => $notifiable->getKey()],
        );

        return (new MailMessage)
            ->subject('Pengaktifan Akaun Sistem ePinjaman')
            ->greeting('Salam '.$notifiable->name)
            ->line('Akaun anda telah dicipta oleh Bahagian Pelaburan Strategik (SID).')
            ->line('Sila aktifkan akaun dan tetapkan kata laluan anda menerusi pautan di bawah. Pautan ini sah selama 24 jam sahaja.')
            ->action('Aktifkan Akaun', $url)
            ->line('Jika anda tidak menjangka e-mel ini, sila abaikan.');
    }
}
