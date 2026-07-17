<?php

namespace Database\Factories;

use App\Models\Permohonan;
use App\Models\Rundingan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rundingan>
 */
class RundinganFactory extends Factory
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
            'direkod_oleh' => null,
            'terma' => fake()->paragraph(),
            'catatan' => null,
            'dipersetujui' => true,
        ];
    }
}
