<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Perjanjian;
use App\Models\PerjanjianUlasan;
use App\Models\User;
use DomainException;

/**
 * SID or BUU records a review comment / negotiation note on the draft
 * agreement (ticket 11).
 */
class UlasPerjanjian
{
    public function handle(Perjanjian $perjanjian, User $user, string $ulasan): PerjanjianUlasan
    {
        if ($perjanjian->permohonan->status !== PermohonanStatus::DalamPerjanjian) {
            throw new DomainException('Permohonan tidak berada pada peringkat penyediaan perjanjian.');
        }

        return $perjanjian->ulasans()->create([
            'user_id' => $user->getKey(),
            'role' => $user->role,
            'ulasan' => $ulasan,
        ]);
    }
}
