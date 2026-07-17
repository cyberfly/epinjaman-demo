<?php

use App\Actions\Permohonan\RekodRundingan;
use App\Actions\Permohonan\SediakanMemo;
use App\Enums\PermohonanStatus;
use App\Enums\TrafficLight;
use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;

test('SID boleh rekod terma rundingan', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamRundingan)->create();

    $rundingan = app(RekodRundingan::class)->handle($permohonan, $psid, 'Kadar faedah 4% setahun');

    expect($rundingan->terma)->toBe('Kadar faedah 4% setahun')
        ->and($rundingan->direkod_oleh)->toBe($psid->id)
        ->and($permohonan->rundingans()->count())->toBe(1);
});

test('Pemohon juga boleh rekod terma rundingan', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamRundingan)->create();

    app(RekodRundingan::class)->handle($permohonan, $user, 'Setuju terma');

    expect($permohonan->rundingans()->count())->toBe(1);
});

test('PSID menyediakan Memo Pertimbangan & permohonan sedia untuk kelulusan', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamRundingan)->create();

    $memo = app(SediakanMemo::class)->handle($permohonan, $psid, 'Terma utama pinjaman...');

    expect($memo->terma_utama)->toBe('Terma utama pinjaman...')
        ->and($memo->peringkat)->toBe(UserRole::PSID)
        ->and($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamKelulusan);
});

test('Memo boleh disediakan tanpa mengira status Traffic Light', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamRundingan)->create(['traffic_light' => TrafficLight::Merah]);

    app(SediakanMemo::class)->handle($permohonan, $psid, 'Terma utama');

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamKelulusan);
});

test('tidak boleh sediakan memo pada status yang salah', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKelulusan)->create();

    expect(fn () => app(SediakanMemo::class)->handle($permohonan, $psid, 'x'))
        ->toThrow(DomainException::class);
});
