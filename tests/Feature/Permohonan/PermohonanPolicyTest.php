<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;

test('pengguna organisasi berkaitan boleh lihat & edit draf sendiri', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::Draf)->create();

    expect($user->can('view', $permohonan))->toBeTrue()
        ->and($user->can('update', $permohonan))->toBeTrue()
        ->and($user->can('submit', $permohonan))->toBeTrue();
});

test('pengguna organisasi lain tidak boleh lihat atau edit permohonan', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Draf)->create();
    $lain = User::factory()->pemohon()->create();

    expect($lain->can('view', $permohonan))->toBeFalse()
        ->and($lain->can('update', $permohonan))->toBeFalse();
});

test('draf tidak boleh diedit selepas dihantar', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::MenungguSemakanKelengkapan)->create();

    expect($user->can('update', $permohonan))->toBeFalse();
});

test('pegawai SID boleh lihat permohonan mana-mana organisasi', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamSemakanSID)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();

    expect($psid->can('view', $permohonan))->toBeTrue()
        ->and($psid->can('update', $permohonan))->toBeFalse();
});
