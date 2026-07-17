<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\SyaratDuluan;
use App\Models\User;
use DomainException;

/**
 * PSID verifies a single uploaded Conditions Precedent item (ticket 13).
 */
class SahkanItemSyaratDuluan
{
    public function handle(SyaratDuluan $item, User $psid): SyaratDuluan
    {
        if ($item->permohonan->status !== PermohonanStatus::DalamPenyediaanCP) {
            throw new DomainException('Permohonan tidak berada pada peringkat Syarat Duluan.');
        }

        if ($item->dokumen_path === null) {
            throw new DomainException('Dokumen Syarat Duluan belum dimuat naik.');
        }

        $item->update([
            'disahkan' => true,
            'disahkan_oleh' => $psid->getKey(),
            'disahkan_pada' => now(),
        ]);

        return $item;
    }
}
