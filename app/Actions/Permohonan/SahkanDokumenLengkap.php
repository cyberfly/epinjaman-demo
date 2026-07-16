<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use DomainException;

/**
 * PSID confirms the documents are complete & orderly, advancing the Permohonan
 * to the negotiation (Rundingan) stage (ticket 06).
 */
class SahkanDokumenLengkap
{
    public function handle(Permohonan $permohonan): Permohonan
    {
        if ($permohonan->status !== PermohonanStatus::DalamSemakanSID) {
            throw new DomainException('Permohonan tidak berada pada peringkat semakan dokumen SID.');
        }

        $permohonan->update([
            'status' => PermohonanStatus::DalamRundingan,
            'traffic_light' => null,
        ]);

        return $permohonan;
    }
}
