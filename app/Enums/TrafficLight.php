<?php

namespace App\Enums;

/**
 * Proactive deadline indicator computed by the daily scheduled job.
 */
enum TrafficLight: string
{
    case Normal = 'normal';
    case Kuning = 'kuning';
    case Merah = 'merah';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::Kuning => 'Kuning — Menghampiri Tarikh Akhir',
            self::Merah => 'Merah — Melepasi Tarikh Akhir',
        };
    }

    /**
     * Flux badge colour for the indicator.
     */
    public function color(): string
    {
        return match ($this) {
            self::Normal => 'green',
            self::Kuning => 'yellow',
            self::Merah => 'red',
        };
    }

    /**
     * Whether reaching this colour warrants a proactive notification.
     */
    public function warrantsNotification(): bool
    {
        return $this === self::Kuning || $this === self::Merah;
    }
}
