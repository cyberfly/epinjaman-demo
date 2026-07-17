<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use DomainException;

/**
 * Locks the application as LENGKAP — only when every CP item is PSID-verified
 * AND the CS field is confirmed. Once locked, the record is read-only
 * (ticket 13).
 */
class KunciPermohonanLengkap
{
    public function handle(Permohonan $permohonan): Permohonan
    {
        if ($permohonan->status !== PermohonanStatus::DalamPenyediaanCP) {
            throw new DomainException('Permohonan tidak berada pada peringkat Syarat Duluan.');
        }

        if (! $permohonan->bolehDikunciLengkap()) {
            throw new DomainException('Semua Syarat Duluan mesti disahkan dan CS ditandakan sebelum dikunci LENGKAP.');
        }

        $permohonan->update([
            'status' => PermohonanStatus::Lengkap,
            'traffic_light' => null,
        ]);

        return $permohonan;
    }
}
