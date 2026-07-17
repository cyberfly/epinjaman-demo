<?php

use App\Actions\TrafficLight\EvaluatePermohonanTrafficLight;
use App\Enums\PermohonanStatus;
use App\Enums\TrafficLight;
use App\Models\Permohonan;
use Illuminate\Support\Carbon;

test('peringkat Setuju Terima menggunakan had 14 hari', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::Ditawarkan)
        ->create(['ditawarkan_pada' => Carbon::parse('2026-05-01')]);

    // Day 12 — within 4-day warning window before the 14-day deadline (May 15)
    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-05-13'));

    expect($assessment)->not->toBeNull()
        ->and($assessment->status)->toBe(TrafficLight::Kuning)
        ->and($assessment->stageLabel)->toBe('Setuju Terima');
});

test('melepasi 14 hari memberi Merah', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::Ditawarkan)
        ->create(['ditawarkan_pada' => Carbon::parse('2026-05-01')]);

    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-05-20'));

    expect($assessment->status)->toBe(TrafficLight::Merah);
});
