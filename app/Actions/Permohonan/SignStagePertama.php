<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Enums\SignatureStage;
use App\Models\DigitalSignature;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use DomainException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

/**
 * Records the Pemohon's stage-1 Digital Signature ("Disediakan" & "Disahkan")
 * on a complete application, then sends it to the controlling ministry
 * (ticket 04). The signature is an internal audited action (ADR-0001).
 */
class SignStagePertama
{
    public function __construct(private CheckPermohonanCompleteness $checkCompleteness) {}

    public function handle(Permohonan $permohonan, User $user): DigitalSignature
    {
        if ($permohonan->status !== PermohonanStatus::MenungguSemakanKelengkapan) {
            throw new DomainException('Permohonan tidak berada pada peringkat Tandatangan Peringkat 1.');
        }

        if (! $this->checkCompleteness->handle($permohonan)->isComplete()) {
            throw ValidationException::withMessages([
                'permohonan' => 'Borang atau dokumen belum lengkap.',
            ]);
        }

        $signature = $permohonan->signatures()->create([
            'user_id' => $user->getKey(),
            'role' => $user->role,
            'stage' => SignatureStage::Peringkat1,
            'statements' => SignatureStage::Peringkat1->statements(),
            'signed_at' => now(),
        ]);

        $permohonan->update([
            'status' => PermohonanStatus::MenungguTandatanganKementerianPengawal,
            'dihantar_ke_kementerian_pada' => now(),
            'traffic_light' => null,
        ]);

        $this->notifyKementerian($permohonan);

        return $signature;
    }

    private function notifyKementerian(Permohonan $permohonan): void
    {
        $kementerian = $permohonan->kementerianPengawal;

        if ($kementerian === null) {
            return;
        }

        Notification::send(
            $kementerian->users,
            new TemplatedNotification('permohonan.dihantar_kementerian', [
                'tajuk' => (string) $permohonan->tajuk,
            ]),
        );
    }
}
