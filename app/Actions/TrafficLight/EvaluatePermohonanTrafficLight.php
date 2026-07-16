<?php

namespace App\Actions\TrafficLight;

use App\Enums\PermohonanStatus;
use App\Models\Permohonan;
use Carbon\CarbonInterface;

/**
 * Maps a Permohonan's current stage to its deadline specification and evaluates
 * the traffic light. Returns null when the current status has no deadline.
 *
 * Later tickets extend deadlineSpec() with additional stages (31 Mac rundingan,
 * 14-day acceptance, 90-day agreement, 60-day CP).
 */
class EvaluatePermohonanTrafficLight
{
    /** Warning window (days) for the fixed-calendar stage deadlines. */
    private const WARNING_DAYS_CALENDAR = 30;

    public function __construct(private TrafficLightEngine $engine) {}

    public function handle(Permohonan $permohonan, ?CarbonInterface $now = null): ?TrafficLightAssessment
    {
        $now ??= now();
        $spec = $this->deadlineSpec($permohonan);

        if ($spec === null) {
            return null;
        }

        [$deadline, $warningDays, $stageLabel] = $spec;

        return new TrafficLightAssessment(
            $this->engine->evaluate($deadline, $now, $warningDays),
            $deadline,
            $stageLabel,
        );
    }

    /**
     * @return array{0: CarbonInterface, 1: int, 2: string}|null
     */
    private function deadlineSpec(Permohonan $permohonan): ?array
    {
        return match ($permohonan->status) {
            PermohonanStatus::MenungguTandatanganKementerianPengawal => [
                $this->next31January($permohonan->dihantar_ke_kementerian_pada ?? $permohonan->created_at),
                self::WARNING_DAYS_CALENDAR,
                'Semakan',
            ],
            default => null,
        };
    }

    /**
     * The next 31 January on or after the reference date.
     */
    private function next31January(CarbonInterface $from): CarbonInterface
    {
        $candidate = $from->copy()->setDate($from->year, 1, 31)->startOfDay();

        return $candidate->greaterThanOrEqualTo($from->copy()->startOfDay())
            ? $candidate
            : $candidate->addYear();
    }
}
