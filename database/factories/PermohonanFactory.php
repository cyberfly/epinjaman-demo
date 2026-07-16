<?php

namespace Database\Factories;

use App\Enums\PermohonanStatus;
use App\Enums\SumberDana;
use App\Models\KementerianPengawal;
use App\Models\Pemohon;
use App\Models\Permohonan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permohonan>
 */
class PermohonanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pemohon_id' => Pemohon::factory(),
            'status' => PermohonanStatus::Draf,
            'tajuk' => fake()->sentence(4),
            'jumlah_dipohon' => fake()->randomFloat(2, 100000, 50000000),
            'tujuan' => fake()->paragraph(),
            'tempoh_bulan' => fake()->numberBetween(12, 120),
            'sumber_dana' => SumberDana::DE,
            'ada_kementerian_pengawal' => false,
            'kementerian_pengawal_id' => null,
        ];
    }

    public function status(PermohonanStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }

    public function de(?KementerianPengawal $kementerian = null): static
    {
        return $this->state(fn (array $attributes) => [
            'sumber_dana' => SumberDana::DE,
            'ada_kementerian_pengawal' => false,
            'kementerian_pengawal_id' => $kementerian?->getKey() ?? KementerianPengawal::factory(),
        ]);
    }

    public function kwapbbDenganKementerian(?KementerianPengawal $kementerian = null): static
    {
        return $this->state(fn (array $attributes) => [
            'sumber_dana' => SumberDana::KWAPBB,
            'ada_kementerian_pengawal' => true,
            'kementerian_pengawal_id' => $kementerian?->getKey() ?? KementerianPengawal::factory(),
        ]);
    }

    public function kwapbbTanpaKementerian(): static
    {
        return $this->state(fn (array $attributes) => [
            'sumber_dana' => SumberDana::KWAPBB,
            'ada_kementerian_pengawal' => false,
            'kementerian_pengawal_id' => null,
        ]);
    }
}
