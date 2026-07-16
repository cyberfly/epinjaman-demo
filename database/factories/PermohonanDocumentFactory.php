<?php

namespace Database\Factories;

use App\Models\Permohonan;
use App\Models\PermohonanDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PermohonanDocument>
 */
class PermohonanDocumentFactory extends Factory
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
            'checklist_item_id' => null,
            'uploaded_by' => null,
            'path' => 'permohonan-documents/'.fake()->uuid().'.pdf',
            'original_name' => fake()->word().'.pdf',
        ];
    }
}
