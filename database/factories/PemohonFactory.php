<?php

namespace Database\Factories;

use App\Enums\PemohonStatus;
use App\Models\Pemohon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pemohon>
 */
class PemohonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->company(),
            'status' => PemohonStatus::Pemohon,
        ];
    }

    public function peminjam(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PemohonStatus::Peminjam,
        ]);
    }
}
