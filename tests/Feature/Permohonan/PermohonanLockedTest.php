<?php

use App\Actions\Permohonan\KunciPermohonanLengkap;
use App\Actions\Permohonan\SahkanCS;
use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Permohonan;
use App\Models\User;

test('permohonan LENGKAP adalah baca-sahaja (isLocked)', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Lengkap)->create();

    expect($permohonan->isLocked())->toBeTrue();
});

test('tiada Action lanjut dibenarkan ke atas permohonan LENGKAP', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Lengkap)->create();

    expect(fn () => app(SahkanCS::class)->handle($permohonan))->toThrow(DomainException::class)
        ->and(fn () => app(KunciPermohonanLengkap::class)->handle($permohonan))->toThrow(DomainException::class);
});

test('policy CP ditolak untuk permohonan LENGKAP', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Lengkap)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();

    expect($psid->can('manageSyaratDuluan', $permohonan))->toBeFalse();
});

test('SID/audit boleh lihat sejarah permohonan LENGKAP', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Lengkap)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();
    $pemohon = User::factory()->pemohon()->create();

    expect($psid->can('viewHistory', $permohonan))->toBeTrue()
        ->and($pemohon->can('viewHistory', $permohonan))->toBeFalse();
});
