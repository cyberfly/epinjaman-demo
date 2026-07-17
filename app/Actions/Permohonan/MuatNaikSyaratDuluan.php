<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Enums\SyaratDuluanJenis;
use App\Models\Permohonan;
use App\Models\SyaratDuluan;
use App\Models\User;
use DomainException;

/**
 * The Peminjam uploads a Conditions Precedent (Syarat Duluan) document for a
 * given jenis — allowed regardless of the traffic light (ticket 13). Re-upload
 * resets the verification.
 */
class MuatNaikSyaratDuluan
{
    public function handle(Permohonan $permohonan, User $user, SyaratDuluanJenis $jenis, string $path, string $nama): SyaratDuluan
    {
        if ($permohonan->status !== PermohonanStatus::DalamPenyediaanCP) {
            throw new DomainException('Permohonan tidak berada pada peringkat Syarat Duluan.');
        }

        return SyaratDuluan::updateOrCreate(
            ['permohonan_id' => $permohonan->getKey(), 'jenis' => $jenis],
            [
                'dokumen_path' => $path,
                'dokumen_nama' => $nama,
                'dimuat_naik_oleh' => $user->getKey(),
                'disahkan' => false,
                'disahkan_oleh' => null,
                'disahkan_pada' => null,
            ],
        );
    }
}
