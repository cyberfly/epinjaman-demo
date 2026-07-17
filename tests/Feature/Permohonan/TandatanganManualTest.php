<?php

use App\Actions\Permohonan\RekodPenyeteman;
use App\Actions\Permohonan\RekodTandatanganManual;
use App\Enums\PemohonStatus;
use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\Perjanjian;
use App\Models\Permohonan;
use App\Models\User;

test('SID merekod tandatangan manual & organisasi bertukar kepada Peminjam', function () {
    $sid = User::factory()->role(UserRole::PSID)->create();
    $pemohon = Pemohon::factory()->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPerjanjian)->create();
    Perjanjian::factory()->for($permohonan)->disahkan()->create();

    app(RekodTandatanganManual::class)->handle($permohonan, $sid, '2026-07-17');

    expect($permohonan->perjanjian->fresh()->tandatangan_manual_selesai)->toBeTrue()
        ->and($pemohon->fresh()->status)->toBe(PemohonStatus::Peminjam);
});

test('tidak boleh rekod tandatangan manual sebelum pengesahan BUU', function () {
    $sid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    Perjanjian::factory()->for($permohonan)->create(); // not BUU-approved

    expect(fn () => app(RekodTandatanganManual::class)->handle($permohonan, $sid, '2026-07-17'))
        ->toThrow(DomainException::class);
});

test('permohonan sedia ada organisasi kekal boleh diakses selepas peralihan Peminjam', function () {
    $sid = User::factory()->role(UserRole::PSID)->create();
    $pemohon = Pemohon::factory()->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPerjanjian)->create();
    $permohonanLama = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::Lengkap)->create();
    Perjanjian::factory()->for($permohonan)->disahkan()->create();

    app(RekodTandatanganManual::class)->handle($permohonan, $sid, '2026-07-17');

    expect($pemohon->fresh()->permohonans()->count())->toBe(2)
        ->and($pemohon->permohonans()->pluck('id'))->toContain($permohonanLama->id);
});

test('Peminjam merekod penyeteman LHDNM & permohonan sedia untuk CP', function () {
    $sid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    Perjanjian::factory()->for($permohonan)->disahkan()->create(['tandatangan_manual_selesai' => true]);

    app(RekodPenyeteman::class)->handle($permohonan, $sid, '2026-07-20');

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamPenyediaanCP)
        ->and($permohonan->fresh()->penyeteman_pada)->not->toBeNull()
        ->and($permohonan->perjanjian->fresh()->penyeteman_selesai)->toBeTrue();
});

test('tidak boleh rekod penyeteman sebelum tandatangan manual', function () {
    $sid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPerjanjian)->create();
    Perjanjian::factory()->for($permohonan)->disahkan()->create(); // manual signing not done

    expect(fn () => app(RekodPenyeteman::class)->handle($permohonan, $sid, '2026-07-20'))
        ->toThrow(DomainException::class);
});
