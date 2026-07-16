<?php

use App\Actions\Permohonan\SubmitPermohonan;
use App\Enums\PermohonanStatus;
use App\Enums\SumberDana;
use App\Models\KementerianPengawal;
use App\Models\Permohonan;
use Illuminate\Validation\ValidationException;

test('permohonan DE dihantar menunggu semakan kelengkapan & tandatangan Peringkat 1', function () {
    $permohonan = Permohonan::factory()->de()->status(PermohonanStatus::Draf)->create();

    app(SubmitPermohonan::class)->handle($permohonan);

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::MenungguSemakanKelengkapan)
        ->and($permohonan->fresh()->dihantar_pada)->not->toBeNull();
});

test('permohonan KWAPBB dengan Kementerian Pengawal mengikut laluan biasa', function () {
    $kementerian = KementerianPengawal::factory()->create();
    $permohonan = Permohonan::factory()->kwapbbDenganKementerian($kementerian)->status(PermohonanStatus::Draf)->create();

    app(SubmitPermohonan::class)->handle($permohonan);

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::MenungguSemakanKelengkapan);
});

test('permohonan KWAPBB tanpa Kementerian Pengawal terus diterima SID (ADR-0002)', function () {
    $permohonan = Permohonan::factory()->kwapbbTanpaKementerian()->status(PermohonanStatus::Draf)->create();

    app(SubmitPermohonan::class)->handle($permohonan);

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::DalamSemakanSID);
});

test('permohonan tidak boleh dihantar tanpa medan wajib', function () {
    $permohonan = Permohonan::factory()->de()->status(PermohonanStatus::Draf)->create([
        'tajuk' => null,
        'jumlah_dipohon' => null,
        'tujuan' => null,
        'tempoh_bulan' => null,
    ]);

    expect(fn () => app(SubmitPermohonan::class)->handle($permohonan))
        ->toThrow(ValidationException::class);

    expect($permohonan->fresh()->status)->toBe(PermohonanStatus::Draf);
});

test('permohonan KWAPBB dengan Kementerian tetapi tanpa memilih kementerian ditolak', function () {
    $permohonan = Permohonan::factory()->status(PermohonanStatus::Draf)->create([
        'sumber_dana' => SumberDana::KWAPBB,
        'ada_kementerian_pengawal' => true,
        'kementerian_pengawal_id' => null,
    ]);

    expect(fn () => app(SubmitPermohonan::class)->handle($permohonan))
        ->toThrow(ValidationException::class);
});
