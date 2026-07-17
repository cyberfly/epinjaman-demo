<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Perjanjian;
use App\Models\Permohonan;
use App\Models\User;
use DomainException;

/**
 * The Pemohon uploads the draft agreement document — allowed regardless of the
 * traffic light (ticket 11). File storage is handled by the caller.
 */
class MuatNaikDrafPerjanjian
{
    public function handle(Permohonan $permohonan, User $user, string $path, string $nama): Perjanjian
    {
        if ($permohonan->status !== PermohonanStatus::DalamPerjanjian) {
            throw new DomainException('Permohonan tidak berada pada peringkat penyediaan perjanjian.');
        }

        return Perjanjian::updateOrCreate(
            ['permohonan_id' => $permohonan->getKey()],
            [
                'draf_path' => $path,
                'draf_nama' => $nama,
                'dimuat_naik_oleh' => $user->getKey(),
                'dimuat_naik_pada' => now(),
            ],
        );
    }
}
