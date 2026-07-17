<?php

use App\Actions\TrafficLight\EvaluatePermohonanTrafficLight;
use App\Enums\PermohonanStatus;
use App\Enums\TrafficLight;
use App\Models\Permohonan;
use Illuminate\Support\Carbon;

test('peringkat Penyediaan Perjanjian menggunakan had 90 hari', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::DalamPerjanjian)
        ->create(['diterima_setuju_pada' => Carbon::parse('2026-01-01')]);

    // Deadline is 2026-04-01; day within the 14-day warning window
    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-03-25'));

    expect($assessment)->not->toBeNull()
        ->and($assessment->status)->toBe(TrafficLight::Kuning)
        ->and($assessment->stageLabel)->toBe('Penyediaan Perjanjian');
});

test('melepasi 90 hari memberi Merah', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::DalamPerjanjian)
        ->create(['diterima_setuju_pada' => Carbon::parse('2026-01-01')]);

    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-05-01'));

    expect($assessment->status)->toBe(TrafficLight::Merah);
});
