<?php

namespace App\Actions\Permohonan;

use App\Enums\PemohonStatus;
use App\Enums\PermohonanStatus;
use App\Models\Perjanjian;
use App\Models\Permohonan;
use App\Models\User;
use DomainException;

/**
 * SID records that the manual signing session of the agreement is complete
 * (status + date + officer, optional scan) — a physical process. The
 * organisation's status then flips from Pemohon to Peminjam (ticket 12).
 */
class RekodTandatanganManual
{
    public function handle(Permohonan $permohonan, User $sid, string $tarikh, ?string $imbasanPath = null): Perjanjian
    {
        if ($permohonan->status !== PermohonanStatus::DalamPerjanjian) {
            throw new DomainException('Permohonan tidak berada pada peringkat perjanjian.');
        }

        $perjanjian = $permohonan->perjanjian;

        if ($perjanjian === null || ! $perjanjian->disahkan_buu) {
            throw new DomainException('Perjanjian belum disahkan oleh BUU.');
        }

        $perjanjian->update([
            'tandatangan_manual_selesai' => true,
            'tandatangan_manual_tarikh' => $tarikh,
            'tandatangan_manual_oleh' => $sid->getKey(),
            'tandatangan_manual_imbasan_path' => $imbasanPath,
        ]);

        $permohonan->pemohon->update(['status' => PemohonStatus::Peminjam]);

        return $perjanjian;
    }
}
