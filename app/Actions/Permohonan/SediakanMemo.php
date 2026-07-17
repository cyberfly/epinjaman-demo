<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Memo;
use App\Models\Permohonan;
use App\Models\User;
use DomainException;

/**
 * PSID prepares the Memo Pertimbangan with draft key terms — allowed regardless
 * of the traffic light — and readies the application for the approval hierarchy
 * (ticket 08). The memo enters the hierarchy at the PSID level.
 */
class SediakanMemo
{
    public function handle(Permohonan $permohonan, User $psid, string $termaUtama, ?string $catatan = null): Memo
    {
        if ($permohonan->status !== PermohonanStatus::DalamRundingan) {
            throw new DomainException('Permohonan tidak berada pada peringkat Rundingan.');
        }

        $memo = Memo::updateOrCreate(
            ['permohonan_id' => $permohonan->getKey()],
            [
                'disediakan_oleh' => $psid->getKey(),
                'terma_utama' => $termaUtama,
                'catatan' => $catatan,
                'peringkat' => UserRole::PSID,
                'keputusan' => null,
                'disediakan_pada' => now(),
            ],
        );

        $permohonan->update([
            'status' => PermohonanStatus::DalamKelulusan,
            'traffic_light' => null,
        ]);

        return $memo;
    }
}
