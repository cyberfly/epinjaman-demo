<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use DomainException;

/**
 * PSID marks the single "Syarat Kemudian (CS) Disahkan" confirmation field —
 * no detailed workflow (out of scope for v1, see CONTEXT.md).
 */
class SahkanCS
{
    public function handle(Permohonan $permohonan): Permohonan
    {
        if ($permohonan->status !== PermohonanStatus::DalamPenyediaanCP) {
            throw new DomainException('Permohonan tidak berada pada peringkat Syarat Duluan.');
        }

        $permohonan->update(['cs_disahkan' => true]);

        return $permohonan;
    }
}
