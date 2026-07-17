<?php

use App\Actions\Permohonan\MuatNaikDrafPerjanjian;
use App\Actions\Permohonan\SahkanPerjanjianBUU;
use App\Actions\Permohonan\UlasPerjanjian;
use App\Enums\PermohonanStatus;
use App\Enums\TrafficLight;
use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\Perjanjian;
use App\Models\Permohonan;
use App\Models\User;

test('Pemohon memuat naik draf perjanjian tanpa mengira Traffic Light', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPerjanjian)->create(['traffic_light' => TrafficLight::Merah]);

    $perjanjian = app(MuatNaikDrafPerjanjian::class)->handle($permohonan, $user, 'perjanjian-draf/a.pdf', 'draf.pdf');

    expect($perjanjian->draf_nama)->toBe('draf.pdf')
        ->and($perjanjian->dimuat_naik_oleh)->toBe($user->id)
        ->and($permohonan->perjanjian)->not->toBeNull();
});

test('SID dan BUU boleh merekod ulasan', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    $perjanjian = Perjanjian::factory()->for($permohonan)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();
    $buu = User::factory()->role(UserRole::BUU)->create();

    app(UlasPerjanjian::class)->handle($perjanjian, $psid, 'Semak klausa 3');
    app(UlasPerjanjian::class)->handle($perjanjian, $buu, 'Perlu pindaan definisi');

    expect($perjanjian->ulasans()->count())->toBe(2);
});

test('BUU mengesahkan perjanjian teratur', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    $perjanjian = Perjanjian::factory()->for($permohonan)->create();
    $buu = User::factory()->role(UserRole::BUU)->create();

    app(SahkanPerjanjianBUU::class)->handle($perjanjian, $buu);

    expect($perjanjian->fresh()->disahkan_buu)->toBeTrue()
        ->and($perjanjian->fresh()->disahkan_oleh)->toBe($buu->id);
});

test('bukan BUU tidak boleh mengesahkan perjanjian', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    $perjanjian = Perjanjian::factory()->for($permohonan)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();

    expect(fn () => app(SahkanPerjanjianBUU::class)->handle($perjanjian, $psid))
        ->toThrow(DomainException::class);
});

test('policy: hanya SID & BUU boleh semak; hanya BUU boleh sahkan', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();
    $buu = User::factory()->role(UserRole::BUU)->create();
    $pemohon = User::factory()->pemohon()->create();

    expect($psid->can('reviewPerjanjian', $permohonan))->toBeTrue()
        ->and($buu->can('reviewPerjanjian', $permohonan))->toBeTrue()
        ->and($pemohon->can('reviewPerjanjian', $permohonan))->toBeFalse()
        ->and($buu->can('approvePerjanjian', $permohonan))->toBeTrue()
        ->and($psid->can('approvePerjanjian', $permohonan))->toBeFalse();
});
