<?php

use App\Actions\TrafficLight\EvaluatePermohonanTrafficLight;
use App\Actions\TrafficLight\EvaluateTrafficLights;
use App\Enums\PermohonanStatus;
use App\Enums\TrafficLight;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Support\Carbon;

test('peringkat Rundingan menggunakan had 31 Mac', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::DalamRundingan)
        ->create(['diterima_sid_pada' => Carbon::parse('2026-01-10')]);

    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-03-15'));

    expect($assessment)->not->toBeNull()
        ->and($assessment->status)->toBe(TrafficLight::Kuning)
        ->and($assessment->deadline->format('Y-m-d'))->toBe('2026-03-31')
        ->and($assessment->stageLabel)->toBe('Rundingan');
});

test('melepasi 31 Mac memberi Merah', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::DalamRundingan)
        ->create(['diterima_sid_pada' => Carbon::parse('2026-01-10')]);

    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-04-05'));

    expect($assessment->status)->toBe(TrafficLight::Merah);
});

test('job harian mengemas kini Traffic Light Rundingan', function () {
    $pemohon = Pemohon::factory()->create();
    User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)
        ->status(PermohonanStatus::DalamRundingan)
        ->create(['diterima_sid_pada' => Carbon::parse('2026-01-10')]);

    app(EvaluateTrafficLights::class)->handle(Carbon::parse('2026-04-05'));

    expect($permohonan->fresh()->traffic_light)->toBe(TrafficLight::Merah);
});
