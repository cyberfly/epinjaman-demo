<?php

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;

test('hanya PSID boleh urus kuiri', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamKuiri)->create();
    $psid = User::factory()->role(UserRole::PSID)->create();
    $pemohon = User::factory()->pemohon()->create();

    expect($psid->can('manageKuiri', $permohonan))->toBeTrue()
        ->and($pemohon->can('manageKuiri', $permohonan))->toBeFalse();
});

test('Pemohon organisasi & Kementerian Pengawal boleh balas; organisasi lain tidak', function () {
    $kementerian = KementerianPengawal::factory()->create();
    $pemohon = Pemohon::factory()->create();
    $permohonan = Permohonan::factory()->for($pemohon)->kwapbbDenganKementerian($kementerian)->status(PermohonanStatus::DalamKuiri)->create();

    $orgUser = User::factory()->pemohon($pemohon)->create();
    $kpUser = User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $kementerian->id]);
    $lain = User::factory()->pemohon()->create();

    expect($orgUser->can('replyKuiri', $permohonan))->toBeTrue()
        ->and($kpUser->can('replyKuiri', $permohonan))->toBeTrue()
        ->and($lain->can('replyKuiri', $permohonan))->toBeFalse();
});
