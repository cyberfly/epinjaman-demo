<?php

use App\Actions\Permohonan\GenerateSuratTawaran;
use App\Actions\Permohonan\TandatanganSuratAkuan;
use App\Enums\PermohonanStatus;
use App\Enums\SignatureStage;
use App\Enums\TrafficLight;
use App\Models\Memo;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Facades\Notification;

test('menjana Surat Tawaran, notifikasi & status Ditawarkan', function () {
    Notification::fake();
    $pemohon = Pemohon::factory()->create();
    User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamKelulusan)->create();
    Memo::factory()->for($permohonan)->create(['terma_utama' => 'Kadar 4%, tempoh 10 tahun']);

    $surat = app(GenerateSuratTawaran::class)->handle($permohonan);

    expect($surat->terma_utama)->toBe('Kadar 4%, tempoh 10 tahun')
        ->and($permohonan->fresh()->status)->toBe(PermohonanStatus::Ditawarkan)
        ->and($permohonan->fresh()->ditawarkan_pada)->not->toBeNull();

    Notification::assertSentTo($pemohon->users->first(), TemplatedNotification::class);
});

test('Pemohon menandatangani Surat Akuan Penerimaan tanpa mengira Traffic Light', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::Ditawarkan)->create(['traffic_light' => TrafficLight::Merah]);

    $signature = app(TandatanganSuratAkuan::class)->handle($permohonan, $user);

    expect($signature->stage)->toBe(SignatureStage::Penerimaan)
        ->and($signature->statements)->toBe(['Setuju Terima'])
        ->and($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamPerjanjian)
        ->and($permohonan->fresh()->diterima_setuju_pada)->not->toBeNull();
});

test('tidak boleh tandatangan akuan pada status yang salah', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPerjanjian)->create();

    expect(fn () => app(TandatanganSuratAkuan::class)->handle($permohonan, $user))
        ->toThrow(DomainException::class);
});
