<?php

use App\Actions\Permohonan\CheckPermohonanCompleteness;
use App\Models\ChecklistItem;
use App\Models\Permohonan;
use App\Models\PermohonanDocument;

test('menyenaraikan medan & dokumen yang tiada', function () {
    $item = ChecklistItem::factory()->create(['label' => 'Penyata kewangan']);
    $permohonan = Permohonan::factory()->create(['tajuk' => null]);

    $result = app(CheckPermohonanCompleteness::class)->handle($permohonan);

    expect($result->isComplete())->toBeFalse()
        ->and($result->missingFields)->toContain('Tajuk / Nama Projek')
        ->and($result->missingDocuments)->toContain('Penyata kewangan');
});

test('lengkap apabila semua medan diisi & setiap item senarai semak ada dokumen', function () {
    $item = ChecklistItem::factory()->create();
    $permohonan = Permohonan::factory()->create();
    PermohonanDocument::factory()->for($permohonan)->create(['checklist_item_id' => $item->id]);

    $result = app(CheckPermohonanCompleteness::class)->handle($permohonan);

    expect($result->isComplete())->toBeTrue();
});

test('item senarai semak tidak aktif tidak dikira sebagai tiada', function () {
    ChecklistItem::factory()->inactive()->create();
    $permohonan = Permohonan::factory()->create();

    $result = app(CheckPermohonanCompleteness::class)->handle($permohonan);

    expect($result->missingDocuments)->toBe([]);
});
