<?php

namespace App\Enums;

/**
 * Organisation status. A Pemohon becomes a Peminjam once the manual signing
 * session of the loan agreement is recorded (see CONTEXT.md).
 */
enum PemohonStatus: string
{
    case Pemohon = 'pemohon';
    case Peminjam = 'peminjam';

    public function label(): string
    {
        return match ($this) {
            self::Pemohon => 'Pemohon',
            self::Peminjam => 'Peminjam',
        };
    }
}
