<?php

namespace App\Actions\Permohonan;

use App\Models\ChecklistItem;
use App\Models\Permohonan;

/**
 * Checks a Permohonan's form fields and supporting documents against the
 * mandatory set and the active Lampiran 6 checklist. Used before allowing the
 * stage-1 signature (ticket 04).
 */
class CheckPermohonanCompleteness
{
    private const REQUIRED_FIELDS = [
        'tajuk' => 'Tajuk / Nama Projek',
        'jumlah_dipohon' => 'Jumlah Dipohon',
        'tujuan' => 'Tujuan',
        'tempoh_bulan' => 'Tempoh',
        'sumber_dana' => 'Sumber Dana',
    ];

    public function handle(Permohonan $permohonan): CompletenessResult
    {
        return new CompletenessResult(
            $this->missingFields($permohonan),
            $this->missingDocuments($permohonan),
        );
    }

    /**
     * @return array<int, string>
     */
    private function missingFields(Permohonan $permohonan): array
    {
        $missing = [];

        foreach (self::REQUIRED_FIELDS as $attribute => $label) {
            if (blank($permohonan->{$attribute})) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    /**
     * @return array<int, string>
     */
    private function missingDocuments(Permohonan $permohonan): array
    {
        $uploadedItemIds = $permohonan->documents()
            ->whereNotNull('checklist_item_id')
            ->pluck('checklist_item_id')
            ->all();

        return ChecklistItem::active()
            ->reject(fn (ChecklistItem $item): bool => in_array($item->id, $uploadedItemIds, true))
            ->map(fn (ChecklistItem $item): string => $item->label)
            ->values()
            ->all();
    }
}
