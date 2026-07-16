<?php

namespace App\Actions\TrafficLight;

use App\Enums\TrafficLight;
use Carbon\CarbonInterface;

/**
 * The evaluated traffic light for a Permohonan at its current stage.
 */
final class TrafficLightAssessment
{
    public function __construct(
        public TrafficLight $status,
        public CarbonInterface $deadline,
        public string $stageLabel,
    ) {}
}
