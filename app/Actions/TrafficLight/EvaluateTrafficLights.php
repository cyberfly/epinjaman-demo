<?php

namespace App\Actions\TrafficLight;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use App\Notifications\TemplatedNotification;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Notification;

/**
 * The daily scheduled evaluation (ticket 04): recomputes the traffic light of
 * every active Permohonan, persists the colour, and dispatches a proactive
 * notification (mail + database) whenever the colour changes to Kuning or Merah.
 */
class EvaluateTrafficLights
{
    public function __construct(private EvaluatePermohonanTrafficLight $evaluator) {}

    /**
     * @return int the number of applications whose colour changed
     */
    public function handle(?CarbonInterface $now = null): int
    {
        $now ??= now();
        $changed = 0;

        Permohonan::query()
            ->whereNotIn('status', [PermohonanStatus::Draf->value, PermohonanStatus::Lengkap->value])
            ->with(['pemohon.users', 'kementerianPengawal.users'])
            ->get()
            ->each(function (Permohonan $permohonan) use ($now, &$changed): void {
                $assessment = $this->evaluator->handle($permohonan, $now);

                if ($assessment === null || $assessment->status === $permohonan->traffic_light) {
                    return;
                }

                $permohonan->update(['traffic_light' => $assessment->status]);
                $changed++;

                if ($assessment->status->warrantsNotification()) {
                    $this->notify($permohonan, $assessment);
                }
            });

        return $changed;
    }

    private function notify(Permohonan $permohonan, TrafficLightAssessment $assessment): void
    {
        $recipients = $permohonan->pemohon->users;

        // The Kementerian Pengawal is only a warning recipient at the Semakan
        // stage (spec story 21); later stages notify the Pemohon/Peminjam only.
        if ($assessment->stageLabel === 'Semakan' && $permohonan->kementerian_pengawal_id !== null) {
            $recipients = $recipients->merge($permohonan->kementerianPengawal->users);
        }

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new TemplatedNotification('traffic_light.amaran', [
            'no_rujukan' => $permohonan->no_rujukan ?? (string) $permohonan->id,
            'peringkat' => $assessment->stageLabel,
            'status' => $assessment->status->label(),
        ]));
    }
}
