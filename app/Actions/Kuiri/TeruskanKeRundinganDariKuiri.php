<?php

namespace App\Actions\Kuiri;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use DomainException;

/**
 * Advance an application from the Kuiri stage to Rundingan — only when every
 * raised Kuiri is Berpuas Hati (ticket 07 gate).
 */
class TeruskanKeRundinganDariKuiri
{
    public function handle(Permohonan $permohonan): Permohonan
    {
        if ($permohonan->status !== PermohonanStatus::DalamKuiri) {
            throw new DomainException('Permohonan tidak berada pada peringkat Kuiri.');
        }

        if (! $permohonan->semuaKuiriBerpuasHati()) {
            throw new DomainException('Masih terdapat Kuiri yang belum Berpuas Hati.');
        }

        $permohonan->update([
            'status' => PermohonanStatus::DalamRundingan,
            'traffic_light' => null,
        ]);

        return $permohonan;
    }
}
