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

    /** Warning window (days) for short relative deadlines (e.g. 14-day). */
    private const WARNING_DAYS_SHORT = 4;

    /** Warning window (days) for medium/long relative deadlines (60/90-day). */
    private const WARNING_DAYS_MEDIUM = 14;

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
                $this->nextAnnualDate($permohonan->dihantar_ke_kementerian_pada ?? $permohonan->created_at, 1, 31),
                self::WARNING_DAYS_CALENDAR,
                'Semakan',
            ],
            PermohonanStatus::DalamRundingan => [
                $this->nextAnnualDate($permohonan->diterima_sid_pada ?? $permohonan->created_at, 3, 31),
                self::WARNING_DAYS_CALENDAR,
                'Rundingan',
            ],
            PermohonanStatus::Ditawarkan => [
                ($permohonan->ditawarkan_pada ?? $permohonan->created_at)->copy()->addDays(14),
                self::WARNING_DAYS_SHORT,
                'Setuju Terima',
            ],
            PermohonanStatus::DalamPerjanjian => [
                ($permohonan->diterima_setuju_pada ?? $permohonan->created_at)->copy()->addDays(90),
                self::WARNING_DAYS_MEDIUM,
                'Penyediaan Perjanjian',
            ],
            default => null,
        };
    }

    /**
     * The next occurrence of month/day on or after the reference date.
     */
    private function nextAnnualDate(CarbonInterface $from, int $month, int $day): CarbonInterface
    {
        $candidate = $from->copy()->setDate($from->year, $month, $day)->startOfDay();

        return $candidate->greaterThanOrEqualTo($from->copy()->startOfDay())
            ? $candidate
            : $candidate->addYear();
    }
}
