<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use DomainException;

/**
 * PSID marks the documents as incomplete/not-orderly, moving the Permohonan
 * into the Kuiri stage where query items will be raised (ticket 06 -> 07).
 */
class TandakanDokumenPerluKuiri
{
    public function handle(Permohonan $permohonan): Permohonan
    {
        if ($permohonan->status !== PermohonanStatus::DalamSemakanSID) {
            throw new DomainException('Permohonan tidak berada pada peringkat semakan dokumen SID.');
        }

        $permohonan->update([
            'status' => PermohonanStatus::DalamKuiri,
            'traffic_light' => null,
        ]);

        return $permohonan;
    }
}
