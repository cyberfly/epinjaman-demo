<?php

namespace App\Enums;

/**
 * A recorded action within the approval hierarchy history (ticket 09).
 */
enum ApprovalStepAction: string
{
    case Endorse = 'endorse';
    case Pulangkan = 'pulangkan';
    case Lulus = 'lulus';
    case TidakLulus = 'tidak_lulus';

    public function label(): string
    {
        return match ($this) {
            self::Endorse => 'Endorse & Hantar',
            self::Pulangkan => 'Pulangkan untuk Pembetulan',
            self::Lulus => 'Lulus',
            self::TidakLulus => 'Tidak Lulus',
        };
    }
}
