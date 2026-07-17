<?php

namespace App\Enums;

/**
 * YB MK's final decision on a Memo Pertimbangan (ticket 09).
 */
enum ApprovalDecision: string
{
    case Lulus = 'lulus';
    case TidakLulus = 'tidak_lulus';

    public function label(): string
    {
        return match ($this) {
            self::Lulus => 'Lulus',
            self::TidakLulus => 'Tidak Lulus',
        };
    }
}
