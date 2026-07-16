<?php

namespace App\Actions\Permohonan;

use App\Models\ChecklistItem;
use App\Models\Permohonan;
use App\Models\PermohonanDocument;
use App\Models\User;

/**
 * Record a supporting document uploaded against a Permohonan, optionally tied
 * to a Lampiran 6 checklist item. File storage is handled by the caller; this
 * action persists the metadata.
 */
class UploadPermohonanDocument
{
    public function handle(
        Permohonan $permohonan,
        User $uploadedBy,
        string $path,
        string $originalName,
        ?ChecklistItem $checklistItem = null,
    ): PermohonanDocument {
        return $permohonan->documents()->create([
            'checklist_item_id' => $checklistItem?->getKey(),
            'uploaded_by' => $uploadedBy->getKey(),
            'path' => $path,
            'original_name' => $originalName,
        ]);
    }
}
