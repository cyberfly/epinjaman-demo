<?php

namespace Database\Factories;

use App\Enums\PemohonStatus;
use App\Enums\SumberDana;
use App\Models\KementerianPengawal;
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

    /**
     * DE organisation: routes through the completeness check & stage-1, so a
     * controlling ministry is required (ADR-0002).
     */
    public function de(?KementerianPengawal $kementerian = null): static
    {
        return $this->state(fn (array $attributes) => [
            'sumber_dana' => SumberDana::DE,
            'ada_kementerian_pengawal' => false,
            'kementerian_pengawal_id' => $kementerian?->getKey() ?? KementerianPengawal::factory(),
        ]);
    }

    /**
     * KWAPBB organisation with a controlling ministry (normal path).
     */
    public function kwapbbDenganKementerian(?KementerianPengawal $kementerian = null): static
    {
        return $this->state(fn (array $attributes) => [
            'sumber_dana' => SumberDana::KWAPBB,
            'ada_kementerian_pengawal' => true,
            'kementerian_pengawal_id' => $kementerian?->getKey() ?? KementerianPengawal::factory(),
        ]);
    }

    /**
     * KWAPBB organisation without a controlling ministry (skip path, ADR-0002).
     */
    public function kwapbbTanpaKementerian(): static
    {
        return $this->state(fn (array $attributes) => [
            'sumber_dana' => SumberDana::KWAPBB,
            'ada_kementerian_pengawal' => false,
            'kementerian_pengawal_id' => null,
        ]);
    }
}
