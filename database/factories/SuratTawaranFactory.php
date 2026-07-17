<?php

namespace Database\Factories;

use App\Models\Permohonan;
use App\Models\SuratTawaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SuratTawaran>
 */
class SuratTawaranFactory extends Factory
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
            'kandungan' => fake()->paragraphs(3, true),
            'terma_utama' => fake()->paragraph(),
            'dijana_pada' => now(),
        ];
    }
}
