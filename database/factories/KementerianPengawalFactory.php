<?php

namespace Database\Factories;

use App\Models\KementerianPengawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KementerianPengawal>
 */
class KementerianPengawalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Kementerian '.fake()->unique()->word(),
        ];
    }
}
