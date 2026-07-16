<?php

namespace App\Actions\TrafficLight;

use App\Enums\TrafficLight;
use Carbon\CarbonInterface;

/**
 * The generic Traffic Light engine (ticket 04). Given a deadline date and a
 * reference moment, it returns Normal / Kuning / Merah. It is deadline-agnostic
 * and reused across every stage that has a time limit.
 */
class TrafficLightEngine
{
    /**
     * @param  int  $warningDays  how many days before the deadline turn Kuning
     */
    public function evaluate(CarbonInterface $deadline, CarbonInterface $now, int $warningDays): TrafficLight
    {
        $deadlineDay = $deadline->copy()->startOfDay();
        $today = $now->copy()->startOfDay();
        $warningStart = $deadlineDay->copy()->subDays($warningDays);

        if ($today->greaterThan($deadlineDay)) {
            return TrafficLight::Merah;
        }

        if ($today->greaterThanOrEqualTo($warningStart)) {
            return TrafficLight::Kuning;
        }

        return TrafficLight::Normal;
    }
}
