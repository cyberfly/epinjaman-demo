<?php

namespace App\Models;

use Database\Factories\NotificationTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Lampiran 8 — an admin-managed notification template, keyed by an event
 * string. Title/content may contain `:token` placeholders interpolated at
 * send time from the notification's data payload.
 *
 * @property int $id
 * @property string $event
 * @property string $title
 * @property string $content
 * @property bool $is_active
 */
class NotificationTemplate extends Model
{
    /** @use HasFactory<NotificationTemplateFactory> */
    use HasFactory;

    protected $fillable = ['event', 'title', 'content', 'is_active'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Resolve the active template for an event (internal API for the system).
     */
    public static function resolve(string $event): ?self
    {
        return static::query()->where('event', $event)->where('is_active', true)->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function renderTitle(array $data): string
    {
        return static::interpolate($this->title, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function renderContent(array $data): string
    {
        return static::interpolate($this->content, $data);
    }

    /**
     * Replace `:token` placeholders with values from the data payload. Unknown
     * tokens are left intact.
     *
     * @param  array<string, mixed>  $data
     */
    protected static function interpolate(string $text, array $data): string
    {
        return (string) preg_replace_callback('/:(\w+)/', function (array $matches) use ($data): string {
            return array_key_exists($matches[1], $data) ? (string) $data[$matches[1]] : $matches[0];
        }, $text);
    }
}
