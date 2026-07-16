<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Proactive traffic light evaluation across all active applications (ticket 04).
Schedule::command('permohonan:traffic-lights')->dailyAt('06:00');
