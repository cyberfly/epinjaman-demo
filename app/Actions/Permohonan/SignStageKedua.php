<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Enums\SignatureStage;
use App\Models\DigitalSignature;
use App\Models\Permohonan;
use App\Models\User;
use DomainException;

/**
 * Records the Kementerian Pengawal's stage-2 Digital Signature ("Disemak" &
 * "Diperaku") — allowed regardless of the traffic light — then forwards the
 * application to SID (ticket 05).
 */
class SignStageKedua
{
    public function __construct(private ReceiveAtSid $receiveAtSid) {}

    public function handle(Permohonan $permohonan, User $user): DigitalSignature
    {
        if ($permohonan->status !== PermohonanStatus::MenungguTandatanganKementerianPengawal) {
            throw new DomainException('Permohonan tidak berada pada peringkat Tandatangan Peringkat 2.');
        }

        $signature = $permohonan->signatures()->create([
            'user_id' => $user->getKey(),
            'role' => $user->role,
            'stage' => SignatureStage::Peringkat2,
            'statements' => SignatureStage::Peringkat2->statements(),
            'signed_at' => now(),
        ]);

        $this->receiveAtSid->handle($permohonan);

        return $signature;
    }
}
