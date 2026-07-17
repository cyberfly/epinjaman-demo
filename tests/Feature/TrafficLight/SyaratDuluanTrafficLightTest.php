<?php

use App\Actions\TrafficLight\EvaluatePermohonanTrafficLight;
use App\Enums\PermohonanStatus;
use App\Enums\TrafficLight;
use App\Models\Permohonan;
use Illuminate\Support\Carbon;

test('peringkat Syarat Duluan menggunakan had 60 hari', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::DalamPenyediaanCP)
        ->create(['penyeteman_pada' => Carbon::parse('2026-01-01')]);

    // Deadline is 2026-03-02; within the 14-day warning window
    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-02-25'));

    expect($assessment)->not->toBeNull()
        ->and($assessment->status)->toBe(TrafficLight::Kuning)
        ->and($assessment->stageLabel)->toBe('Syarat Duluan');
});

test('melepasi 60 hari memberi Merah', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::DalamPenyediaanCP)
        ->create(['penyeteman_pada' => Carbon::parse('2026-01-01')]);

    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-03-20'));

    expect($assessment->status)->toBe(TrafficLight::Merah);
});
