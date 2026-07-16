<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;

/**
 * Return an application to the Pemohon as a draft for correction (e.g. when the
 * completeness check fails during stage-1). The missing-field/document
 * indicators are surfaced separately by CheckPermohonanCompleteness.
 */
class ReturnPermohonanToDraft
{
    public function handle(Permohonan $permohonan): Permohonan
    {
        $permohonan->update([
            'status' => PermohonanStatus::Draf,
            'traffic_light' => null,
        ]);

        return $permohonan;
    }
}
