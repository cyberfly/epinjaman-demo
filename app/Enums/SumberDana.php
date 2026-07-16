<?php

namespace App\Enums;

/**
 * Funding source for a Permohonan. Determines the review routing (see ADR-0002).
 */
enum SumberDana: string
{
    case DE = 'de';
    case KWAPBB = 'kwapbb';

    public function label(): string
    {
        return match ($this) {
            self::DE => 'Dana Ekuiti (DE)',
            self::KWAPBB => 'KWAPBB',
        };
    }
}
