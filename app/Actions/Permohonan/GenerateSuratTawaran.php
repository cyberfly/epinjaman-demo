<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use App\Models\SuratTawaran;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Auto-generates the official offer letter and Key Terms & Conditions
 * attachment on approval, moves the application to the Setuju Terima stage, and
 * notifies the Pemohon (ticket 10).
 */
class GenerateSuratTawaran
{
    public function handle(Permohonan $permohonan): SuratTawaran
    {
        // A memo is guaranteed at this point (generated only on approval).
        $terma = $permohonan->memo->terma_utama;

        $surat = SuratTawaran::create([
            'permohonan_id' => $permohonan->getKey(),
            'kandungan' => $this->composeLetter($permohonan),
            'terma_utama' => $terma,
            'dijana_pada' => now(),
        ]);

        $permohonan->update([
            'status' => PermohonanStatus::Ditawarkan,
            'ditawarkan_pada' => now(),
            'traffic_light' => null,
        ]);

        Notification::send(
            $permohonan->pemohon->users,
            new TemplatedNotification('tawaran.dijana', [
                'no_rujukan' => $permohonan->no_rujukan ?? (string) $permohonan->getKey(),
            ]),
        );

        return $surat;
    }

    private function composeLetter(Permohonan $permohonan): string
    {
        return sprintf(
            "SURAT TAWARAN PINJAMAN\nNo. Rujukan: %s\nPemohon: %s\nJumlah: RM %s\n\nDengan hormatnya dimaklumkan bahawa permohonan pinjaman anda telah DILULUSKAN tertakluk kepada terma & syarat utama yang dilampirkan.",
            $permohonan->no_rujukan ?? '-',
            $permohonan->pemohon->nama,
            $permohonan->jumlah_dipohon ?? '-',
        );
    }
}
