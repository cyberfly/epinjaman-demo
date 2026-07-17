<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Enums\SignatureStage;
use App\Models\DigitalSignature;
use App\Models\Permohonan;
use App\Models\User;
use DomainException;

/**
 * The Pemohon reviews the offer and applies the Digital Signature on the Surat
 * Akuan Penerimaan — allowed regardless of the traffic light — moving the
 * application to the agreement-preparation stage (ticket 10).
 */
class TandatanganSuratAkuan
{
    public function handle(Permohonan $permohonan, User $user): DigitalSignature
    {
        if ($permohonan->status !== PermohonanStatus::Ditawarkan) {
            throw new DomainException('Permohonan tidak berada pada peringkat Setuju Terima.');
        }

        $signature = $permohonan->signatures()->create([
            'user_id' => $user->getKey(),
            'role' => $user->role,
            'stage' => SignatureStage::Penerimaan,
            'statements' => SignatureStage::Penerimaan->statements(),
            'signed_at' => now(),
        ]);

        $permohonan->update([
            'status' => PermohonanStatus::DalamPerjanjian,
            'diterima_setuju_pada' => now(),
            'traffic_light' => null,
        ]);

        return $signature;
    }
}
