<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use App\Models\Rundingan;
use App\Models\User;
use DomainException;

/**
 * Records a negotiation session's agreed loan terms. Either SID or the Pemohon
 * may record (ticket 08).
 */
class RekodRundingan
{
    public function handle(Permohonan $permohonan, User $user, string $terma, ?string $catatan = null): Rundingan
    {
        if ($permohonan->status !== PermohonanStatus::DalamRundingan) {
            throw new DomainException('Permohonan tidak berada pada peringkat Rundingan.');
        }

        return $permohonan->rundingans()->create([
            'direkod_oleh' => $user->getKey(),
            'terma' => $terma,
            'catatan' => $catatan,
            'dipersetujui' => true,
        ]);
    }
}
