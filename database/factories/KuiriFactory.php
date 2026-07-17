<?php

namespace Database\Factories;

use App\Enums\KuiriStatus;
use App\Models\Kuiri;
use App\Models\Permohonan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kuiri>
 */
class KuiriFactory extends Factory
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
            'dicetus_oleh' => null,
            'tajuk' => fake()->sentence(3),
            'sebab' => fake()->paragraph(),
            'status' => KuiriStatus::Terbuka,
            'tarikh_akhir' => now()->addDays(7),
        ];
    }

    public function status(KuiriStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }

    public function berpuasHati(): static
    {
        return $this->status(KuiriStatus::BerpuasHati);
    }
}
