<?php

namespace Database\Factories;

use App\Enums\SyaratDuluanJenis;
use App\Models\Permohonan;
use App\Models\SyaratDuluan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SyaratDuluan>
 */
class SyaratDuluanFactory extends Factory
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
            'jenis' => SyaratDuluanJenis::Cagaran,
            'dokumen_path' => 'syarat-duluan/'.fake()->uuid().'.pdf',
            'dokumen_nama' => 'dokumen.pdf',
            'disahkan' => false,
        ];
    }

    public function jenis(SyaratDuluanJenis $jenis): static
    {
        return $this->state(fn (array $attributes) => ['jenis' => $jenis]);
    }

    public function disahkan(): static
    {
        return $this->state(fn (array $attributes) => [
            'disahkan' => true,
            'disahkan_pada' => now(),
        ]);
    }
}
