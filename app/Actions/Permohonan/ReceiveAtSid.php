<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Receive an application at SID: assign a unique reference number, move it into
 * the PSID review tray, and email the Pemohon an acknowledgement. Reached from
 * two paths — after stage-2 (KP signature), and directly for the
 * KWAPBB-without-ministry skip path (ADR-0002).
 */
class ReceiveAtSid
{
    public function handle(Permohonan $permohonan): Permohonan
    {
        $attributes = [
            'status' => PermohonanStatus::DalamSemakanSID,
            'traffic_light' => null,
            'diterima_sid_pada' => now(),
        ];

        if ($permohonan->no_rujukan === null) {
            $attributes['no_rujukan'] = $this->generateNoRujukan($permohonan);
        }

        $permohonan->update($attributes);

        Notification::send(
            $permohonan->pemohon->users,
            new TemplatedNotification('permohonan.diterima', [
                'no_rujukan' => $permohonan->no_rujukan,
            ]),
        );

        return $permohonan;
    }

    private function generateNoRujukan(Permohonan $permohonan): string
    {
        return sprintf('SID/%d/%04d', now()->year, $permohonan->getKey());
    }
}
