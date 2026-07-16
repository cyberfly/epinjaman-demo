<?php

namespace App\Notifications;

use App\Models\NotificationTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A generic notification driven by an admin-managed template (Lampiran 8).
 * Resolves the template by event, interpolates the data payload, and delivers
 * over both the mail and database channels.
 */
class TemplatedNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public string $event,
        public array $data = [],
        public ?string $actionUrl = null,
        public ?string $actionText = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        [$title, $content] = $this->resolveTemplate();

        $mail = (new MailMessage)->subject($title)->line($content);

        if ($this->actionUrl !== null) {
            $mail->action($this->actionText ?? __('Lihat'), $this->actionUrl);
        }

        return $mail;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        [$title, $content] = $this->resolveTemplate();

        return array_merge([
            'event' => $this->event,
            'title' => $title,
            'content' => $content,
            'action_url' => $this->actionUrl,
        ], $this->data);
    }

    /**
     * Resolve and render the [title, content] for this event.
     *
     * @return array{0: string, 1: string}
     */
    protected function resolveTemplate(): array
    {
        $template = NotificationTemplate::resolve($this->event);

        if ($template === null) {
            return [$this->event, ''];
        }

        return [$template->renderTitle($this->data), $template->renderContent($this->data)];
    }
}
