<?php

namespace Database\Factories;

use App\Models\ChecklistItem;
use App\Models\DocumentReview;
use App\Models\Permohonan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentReview>
 */
class DocumentReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'permohonan_id' => Permohonan::factory(),
            'checklist_item_id' => ChecklistItem::factory(),
            'reviewed_by' => null,
            'is_complete' => true,
            'is_orderly' => true,
            'catatan' => null,
        ];
    }
}
