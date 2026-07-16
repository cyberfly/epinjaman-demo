<?php

use App\Actions\TrafficLight\EvaluateTrafficLights;
use App\Enums\PermohonanStatus;
use App\Enums\TrafficLight;
use App\Enums\UserRole;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

function stagedPermohonan(): Permohonan
{
    $pemohon = Pemohon::factory()->create();
    User::factory()->pemohon($pemohon)->create();
    $kementerian = KementerianPengawal::factory()->create();
    User::factory()->role(UserRole::KementerianPengawal)->create(['kementerian_pengawal_id' => $kementerian->id]);

    return Permohonan::factory()
        ->for($pemohon)
        ->kwapbbDenganKementerian($kementerian)
        ->status(PermohonanStatus::MenungguTandatanganKementerianPengawal)
        ->create(['dihantar_ke_kementerian_pada' => Carbon::parse('2025-11-01')]);
}

test('job berjadual mengemas kini status Traffic Light dan menghantar notifikasi apabila Merah', function () {
    Notification::fake();
    $permohonan = stagedPermohonan();

    $changed = app(EvaluateTrafficLights::class)->handle(Carbon::parse('2026-02-15'));

    expect($changed)->toBe(1)
        ->and($permohonan->fresh()->traffic_light)->toBe(TrafficLight::Merah);

    Notification::assertSentTo($permohonan->pemohon->users->first(), TemplatedNotification::class);
    Notification::assertSentTo($permohonan->kementerianPengawal->users->first(), TemplatedNotification::class);
});

test('tiada notifikasi apabila warna kekal sama', function () {
    Notification::fake();
    $permohonan = stagedPermohonan();
    $permohonan->update(['traffic_light' => TrafficLight::Merah]);

    $changed = app(EvaluateTrafficLights::class)->handle(Carbon::parse('2026-02-15'));

    expect($changed)->toBe(0);
    Notification::assertNothingSent();
});

test('status Normal tidak mencetuskan notifikasi', function () {
    Notification::fake();
    $permohonan = stagedPermohonan();

    $changed = app(EvaluateTrafficLights::class)->handle(Carbon::parse('2025-11-05'));

    expect($changed)->toBe(1)
        ->and($permohonan->fresh()->traffic_light)->toBe(TrafficLight::Normal);
    Notification::assertNothingSent();
});

test('command permohonan:traffic-lights berjalan', function () {
    stagedPermohonan();

    $this->artisan('permohonan:traffic-lights')->assertSuccessful();
});
