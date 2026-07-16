<?php

use App\Actions\Permohonan\SignStageKedua;
use App\Enums\PermohonanStatus;
use App\Enums\SignatureStage;
use App\Enums\TrafficLight;
use App\Enums\UserRole;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;

test('Kementerian Pengawal turunkan Tandatangan Peringkat 2 & permohonan dihantar ke SID', function () {
    Notification::fake();
    $kementerian = KementerianPengawal::factory()->create();
    $kpUser = User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $kementerian->id]);
    $pemohon = Pemohon::factory()->create();
    User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()
        ->for($pemohon)
        ->kwapbbDenganKementerian($kementerian)
        ->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)
        ->create();

    $signature = app(SignStageKedua::class)->handle($permohonan, $kpUser);

    expect($signature->stage)->toBe(SignatureStage::Peringkat2)
        ->and($signature->statements)->toBe(['Disemak', 'Diperaku'])
        ->and($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamSemakanSID)
        ->and($permohonan->fresh()->no_rujukan)->not->toBeNull();

    Notification::assertSentTo($pemohon->users->first(), TemplatedNotification::class);
});

test('Tandatangan Peringkat 2 dibenarkan tanpa mengira status Traffic Light', function () {
    $kementerian = KementerianPengawal::factory()->create();
    $kpUser = User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $kementerian->id]);
    $permohonan = Permohonan::factory()
        ->kwapbbDenganKementerian($kementerian)
        ->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)
        ->create(['traffic_light' => TrafficLight::Merah]);

    app(SignStageKedua::class)->handle($permohonan, $kpUser);

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamSemakanSID);
});

test('tidak boleh tandatangan Peringkat 2 pada status yang salah', function () {
    $kementerian = KementerianPengawal::factory()->create();
    $kpUser = User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $kementerian->id]);
    $permohonan = Permohonan::factory()->kwapbbDenganKementerian($kementerian)->status(PermohonanStatus::Draf)->create();

    expect(fn () => app(SignStageKedua::class)->handle($permohonan, $kpUser))
        ->toThrow(DomainException::class);
});
