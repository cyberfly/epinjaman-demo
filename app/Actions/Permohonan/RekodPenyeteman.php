<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Perjanjian;
use App\Models\Permohonan;
use App\Models\User;
use DomainException;

/**
 * Records the LHDNM stamping & duty-stamp confirmation (status + date + officer)
 * — a physical process — readying the application for the Syarat Duluan stage
 * (ticket 12 -> 13).
 */
class RekodPenyeteman
{
    public function handle(Permohonan $permohonan, User $user, string $tarikh): Perjanjian
    {
        if ($permohonan->status !== PermohonanStatus::DalamPerjanjian) {
            throw new DomainException('Permohonan tidak berada pada peringkat perjanjian.');
        }

        $perjanjian = $permohonan->perjanjian;

        if ($perjanjian === null || ! $perjanjian->tandatangan_manual_selesai) {
            throw new DomainException('Sesi Tandatangan Manual belum direkod.');
        }

        $perjanjian->update([
            'penyeteman_selesai' => true,
            'penyeteman_tarikh' => $tarikh,
            'penyeteman_oleh' => $user->getKey(),
        ]);

        $permohonan->update([
            'status' => PermohonanStatus::DalamPenyediaanCP,
            'penyeteman_pada' => now(),
            'traffic_light' => null,
        ]);

        return $perjanjian;
    }
}
