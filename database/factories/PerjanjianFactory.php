<?php

namespace Database\Factories;

use App\Models\Perjanjian;
use App\Models\Permohonan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Perjanjian>
 */
class PerjanjianFactory extends Factory
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
            'draf_path' => 'perjanjian-draf/'.fake()->uuid().'.pdf',
            'draf_nama' => 'draf-perjanjian.pdf',
            'dimuat_naik_pada' => now(),
            'disahkan_buu' => false,
        ];
    }

    public function disahkan(): static
    {
        return $this->state(fn (array $attributes) => [
            'disahkan_buu' => true,
            'disahkan_pada' => now(),
        ]);
    }
}
