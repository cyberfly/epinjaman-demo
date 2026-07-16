<?php

use App\Actions\Permohonan\SaveDraftPermohonan;
use App\Enums\PermohonanStatus;
use App\Enums\SumberDana;
use App\Models\Pemohon;

test('mencipta draf permohonan baharu untuk organisasi', function () {
    $pemohon = Pemohon::factory()->create();

    $permohonan = app(SaveDraftPermohonan::class)->handle($pemohon, [
        'tajuk' => 'Projek Infrastruktur',
        'jumlah_dipohon' => 1000000,
        'tujuan' => 'Pembinaan jalan',
        'tempoh_bulan' => 60,
        'sumber_dana' => SumberDana::DE->value,
    ]);

    expect($permohonan->status)->toBe(PermohonanStatus::Draf)
        ->and($permohonan->pemohon_id)->toBe($pemohon->id)
        ->and($permohonan->tajuk)->toBe('Projek Infrastruktur');
});

test('mengemas kini draf sedia ada', function () {
    $pemohon = Pemohon::factory()->create();
    $draft = app(SaveDraftPermohonan::class)->handle($pemohon, ['tajuk' => 'Asal']);

    app(SaveDraftPermohonan::class)->handle($pemohon, ['tajuk' => 'Dikemaskini'], $draft);

    expect($draft->fresh()->tajuk)->toBe('Dikemaskini');
});

test('satu organisasi boleh mempunyai berbilang permohonan', function () {
    $pemohon = Pemohon::factory()->create();

    app(SaveDraftPermohonan::class)->handle($pemohon, ['tajuk' => 'Pertama']);
    app(SaveDraftPermohonan::class)->handle($pemohon, ['tajuk' => 'Kedua']);

    expect($pemohon->permohonans()->count())->toBe(2);
});
