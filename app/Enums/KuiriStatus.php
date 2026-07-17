<?php

namespace App\Enums;

/**
 * Status of a single Kuiri item. Both Terbuka and TidakBerpuasHati are treated
 * as "active" (unsatisfied); only BerpuasHati closes the item.
 */
enum KuiriStatus: string
{
    case Terbuka = 'terbuka';
    case TidakBerpuasHati = 'tidak_berpuas_hati';
    case BerpuasHati = 'berpuas_hati';

    public function label(): string
    {
        return match ($this) {
            self::Terbuka => 'Terbuka',
            self::TidakBerpuasHati => 'Tidak Berpuas Hati',
            self::BerpuasHati => 'Berpuas Hati',
        };
    }

    public function isSatisfied(): bool
    {
        return $this === self::BerpuasHati;
    }
}
