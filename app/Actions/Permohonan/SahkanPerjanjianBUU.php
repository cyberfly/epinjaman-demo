<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Perjanjian;
use App\Models\User;
use DomainException;

/**
 * BUU confirms the draft agreement is orderly, readying the application for the
 * manual signing session (ticket 11 -> 12).
 */
class SahkanPerjanjianBUU
{
    public function handle(Perjanjian $perjanjian, User $buu): Perjanjian
    {
        if ($perjanjian->permohonan->status !== PermohonanStatus::DalamPerjanjian) {
            throw new DomainException('Permohonan tidak berada pada peringkat penyediaan perjanjian.');
        }

        if ($buu->role !== UserRole::BUU) {
            throw new DomainException('Hanya BUU boleh mengesahkan perjanjian.');
        }

        $perjanjian->update([
            'disahkan_buu' => true,
            'disahkan_oleh' => $buu->getKey(),
            'disahkan_pada' => now(),
        ]);

        return $perjanjian;
    }
}
