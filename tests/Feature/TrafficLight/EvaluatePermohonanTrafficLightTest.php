<?php

use App\Actions\TrafficLight\EvaluatePermohonanTrafficLight;
use App\Enums\PermohonanStatus;
use App\Enums\TrafficLight;
use App\Models\Permohonan;
use Illuminate\Support\Carbon;

test('peringkat Semakan menggunakan had 31 Januari', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)
        ->create(['dihantar_ke_kementerian_pada' => Carbon::parse('2025-11-01')]);

    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-01-20'));

    expect($assessment)->not->toBeNull()
        ->and($assessment->status)->toBe(TrafficLight::Kuning)
        ->and($assessment->deadline->format('Y-m-d'))->toBe('2026-01-31')
        ->and($assessment->stageLabel)->toBe('Semakan');
});

test('had 31 Januari melepasi memberi Merah', function () {
    $permohonan = Permohonan::factory()
        ->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)
        ->create(['dihantar_ke_kementerian_pada' => Carbon::parse('2025-11-01')]);

    $assessment = app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-02-10'));

    expect($assessment->status)->toBe(TrafficLight::Merah);
});

test('status tanpa tarikh had memberi null', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Draf)->create();

    expect(app(EvaluatePermohonanTrafficLight::class)->handle($permohonan, Carbon::parse('2026-01-20')))->toBeNull();
});
