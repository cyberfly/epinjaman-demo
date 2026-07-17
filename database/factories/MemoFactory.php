<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Memo;
use App\Models\Permohonan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Memo>
 */
class MemoFactory extends Factory
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
            'disediakan_oleh' => null,
            'terma_utama' => fake()->paragraphs(2, true),
            'catatan' => null,
            'peringkat' => UserRole::PSID,
            'keputusan' => null,
            'disediakan_pada' => now(),
        ];
    }

    public function peringkat(UserRole $peringkat): static
    {
        return $this->state(fn (array $attributes) => ['peringkat' => $peringkat]);
    }
}
