<?php

use App\Actions\Permohonan\UploadPermohonanDocument;
use App\Models\ChecklistItem;
use App\Models\Pemohon;
use App\Models\Permohonan;
use App\Models\User;

test('merekod dokumen sokongan yang dimuat naik terhadap permohonan', function () {
    $pemohon = Pemohon::factory()->create();
    $user = User::factory()->pemohon($pemohon)->create();
    $permohonan = Permohonan::factory()->for($pemohon)->create();
    $item = ChecklistItem::factory()->create();

    $document = app(UploadPermohonanDocument::class)->handle(
        $permohonan,
        $user,
        'permohonan-documents/abc.pdf',
        'surat.pdf',
        $item,
    );

    expect($document->permohonan_id)->toBe($permohonan->id)
        ->and($document->checklist_item_id)->toBe($item->id)
        ->and($document->uploaded_by)->toBe($user->id)
        ->and($document->original_name)->toBe('surat.pdf');
});
