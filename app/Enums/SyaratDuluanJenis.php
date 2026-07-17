<?php

namespace App\Enums;

/**
 * The defined list of Conditions Precedent (Syarat Duluan) documents the
 * Peminjam must complete before the application can be locked LENGKAP.
 */
enum SyaratDuluanJenis: string
{
    case Cagaran = 'cagaran';
    case Gadaian = 'gadaian';
    case BuktiAkaun = 'bukti_akaun';

    public function label(): string
    {
        return match ($this) {
            self::Cagaran => 'Cagaran',
            self::Gadaian => 'Gadaian',
            self::BuktiAkaun => 'Bukti Akaun / SLA',
        };
    }
}
