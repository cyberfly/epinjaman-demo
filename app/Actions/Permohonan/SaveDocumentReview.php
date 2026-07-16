<?php

namespace App\Actions\Permohonan;

use App\Models\ChecklistItem;
use App\Models\DocumentReview;
use App\Models\Permohonan;
use App\Models\User;

/**
 * Persist PSID's per-item review of a checklist item (complete/incomplete and
 * orderly/not-orderly) for a Permohonan (ticket 06).
 */
class SaveDocumentReview
{
    public function handle(
        Permohonan $permohonan,
        ChecklistItem $checklistItem,
        User $reviewer,
        bool $isComplete,
        bool $isOrderly,
        ?string $catatan = null,
    ): DocumentReview {
        return DocumentReview::updateOrCreate(
            [
                'permohonan_id' => $permohonan->getKey(),
                'checklist_item_id' => $checklistItem->getKey(),
            ],
            [
                'reviewed_by' => $reviewer->getKey(),
                'is_complete' => $isComplete,
                'is_orderly' => $isOrderly,
                'catatan' => $catatan,
            ],
        );
    }
}
