<?php

use App\Actions\Permohonan\KunciPermohonanLengkap;
use App\Actions\Permohonan\MuatNaikSyaratDuluan;
use App\Actions\Permohonan\SahkanCS;
use App\Actions\Permohonan\SahkanItemSyaratDuluan;
use App\Enums\PermohonanStatus;
use App\Enums\SyaratDuluanJenis;
use App\Enums\TrafficLight;
use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;

/**
 * Upload + verify every CP jenis for a permohonan.
 */
function lengkapkanCp(Permohonan $permohonan, User $peminjam, User $psid): void
{
    foreach (SyaratDuluanJenis::cases() as $jenis) {
        $item = app(MuatNaikSyaratDuluan::class)->handle($permohonan, $peminjam, $jenis, "cp/{$jenis->value}.pdf", "{$jenis->value}.pdf");
        app(SahkanItemSyaratDuluan::class)->handle($item, $psid);
    }
}

test('Peminjam memuat naik CP tanpa mengira Traffic Light', function () {
    $pemohon = Pemohon::factory()->peminjam()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPenyediaanCP)->create(['traffic_light' => TrafficLight::Merah]);

    $item = app(MuatNaikSyaratDuluan::class)->handle($permohonan, $user, SyaratDuluanJenis::Cagaran, 'cp/a.pdf', 'cagaran.pdf');

    expect($item->jenis)->toBe(SyaratDuluanJenis::Cagaran)
        ->and($item->disahkan)->toBeFalse();
});

test('PSID mengesahkan setiap item CP', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPenyediaanCP)->create();
    $peminjam = User::factory()->pemohon()->create();
    $item = app(MuatNaikSyaratDuluan::class)->handle($permohonan, $peminjam, SyaratDuluanJenis::Gadaian, 'cp/g.pdf', 'gadaian.pdf');

    app(SahkanItemSyaratDuluan::class)->handle($item, $psid);

    expect($item->fresh()->disahkan)->toBeTrue()
        ->and($item->fresh()->disahkan_oleh)->toBe($psid->id);
});

test('permohonan dikunci LENGKAP hanya apabila semua CP disahkan DAN CS ditandakan', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $pemohon = Pemohon::factory()->peminjam()->create();
    $peminjam = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->status(PermohonanStatus::DalamPenyediaanCP)->create();

    lengkapkanCp($permohonan, $peminjam, $psid);

    // CS not yet confirmed — cannot lock
    expect(fn () => app(KunciPermohonanLengkap::class)->handle($permohonan->fresh()))
        ->toThrow(DomainException::class);

    app(SahkanCS::class)->handle($permohonan->fresh());

    app(KunciPermohonanLengkap::class)->handle($permohonan->fresh());

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::Lengkap);
});

test('tidak boleh kunci LENGKAP jika ada CP belum disahkan', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPenyediaanCP)->create(['cs_disahkan' => true]);
    $peminjam = User::factory()->pemohon()->create();

    // Only one of the CP jenis uploaded/verified
    $item = app(MuatNaikSyaratDuluan::class)->handle($permohonan, $peminjam, SyaratDuluanJenis::Cagaran, 'cp/a.pdf', 'a.pdf');
    app(SahkanItemSyaratDuluan::class)->handle($item, $psid);

    expect(fn () => app(KunciPermohonanLengkap::class)->handle($permohonan->fresh()))
        ->toThrow(DomainException::class);
});

test('memuat naik semula CP menetapkan semula pengesahan', function () {
    $psid = User::factory()->role(UserRole::PSID)->create();
    $permohonan = Permohonan::factory()->status(PermohonanStatus::DalamPenyediaanCP)->create();
    $peminjam = User::factory()->pemohon()->create();

    $item = app(MuatNaikSyaratDuluan::class)->handle($permohonan, $peminjam, SyaratDuluanJenis::Cagaran, 'cp/a.pdf', 'a.pdf');
    app(SahkanItemSyaratDuluan::class)->handle($item, $psid);
    expect($item->fresh()->disahkan)->toBeTrue();

    app(MuatNaikSyaratDuluan::class)->handle($permohonan, $peminjam, SyaratDuluanJenis::Cagaran, 'cp/a2.pdf', 'a2.pdf');
    expect($item->fresh()->disahkan)->toBeFalse();
});
