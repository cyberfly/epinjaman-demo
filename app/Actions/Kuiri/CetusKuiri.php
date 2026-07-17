<?php

namespace App\Actions\Kuiri;

use App\Enums\KuiriStatus;
use App\Enums\PermohonanStatus;
use App\Models\Kuiri;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use DomainException;
use Illuminate\Support\Facades\Notification;

/**
 * PSID raises a single Kuiri item against a Permohonan (ticket 07). Several
 * Kuiri may be active at once. A notification (mail + database) is sent to the
 * Pemohon and, when applicable, the Kementerian Pengawal, with a 7-day reply
 * deadline.
 */
class CetusKuiri
{
    public function handle(Permohonan $permohonan, User $psid, string $tajuk, string $sebab): Kuiri
    {
        if (! in_array($permohonan->status, [PermohonanStatus::DalamSemakanSID, PermohonanStatus::DalamKuiri], true)) {
            throw new DomainException('Kuiri hanya boleh dicetuskan semasa semakan SID.');
        }

        $kuiri = $permohonan->kuiris()->create([
            'dicetus_oleh' => $psid->getKey(),
            'tajuk' => $tajuk,
            'sebab' => $sebab,
            'status' => KuiriStatus::Terbuka,
            'tarikh_akhir' => now()->addDays(7),
        ]);

        if ($permohonan->status !== PermohonanStatus::DalamKuiri) {
            $permohonan->update([
                'status' => PermohonanStatus::DalamKuiri,
                'traffic_light' => null,
            ]);
        }

        $this->notify($permohonan, $kuiri);

        return $kuiri;
    }

    private function notify(Permohonan $permohonan, Kuiri $kuiri): void
    {
        $recipients = $permohonan->pemohon->users;

        if ($permohonan->kementerian_pengawal_id !== null) {
            $recipients = $recipients->merge($permohonan->kementerianPengawal->users);
        }

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new TemplatedNotification('kuiri.dicetus', [
            'no_rujukan' => $permohonan->no_rujukan ?? (string) $permohonan->id,
            'sebab' => $kuiri->sebab,
            'tarikh_akhir' => $kuiri->tarikh_akhir?->format('d/m/Y') ?? '',
        ]));
    }
}
