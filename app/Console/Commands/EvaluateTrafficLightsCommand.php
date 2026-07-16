<?php

namespace App\Console\Commands;

use App\Actions\TrafficLight\EvaluateTrafficLights;
use Illuminate\Console\Command;

class EvaluateTrafficLightsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'permohonan:traffic-lights';

    /**
     * @var string
     */
    protected $description = 'Evaluate active applications against their stage deadlines and send Kuning/Merah notifications.';

    public function handle(EvaluateTrafficLights $evaluateTrafficLights): int
    {
        $changed = $evaluateTrafficLights->handle();

        $this->info("Traffic light status updated for {$changed} permohonan.");

        return self::SUCCESS;
    }
}
