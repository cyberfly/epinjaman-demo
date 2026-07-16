<?php

namespace Database\Factories;

use App\Enums\SignatureStage;
use App\Enums\UserRole;
use App\Models\DigitalSignature;
use App\Models\Permohonan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DigitalSignature>
 */
class DigitalSignatureFactory extends Factory
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
            'user_id' => User::factory(),
            'role' => UserRole::Pemohon,
            'stage' => SignatureStage::Peringkat1,
            'statements' => SignatureStage::Peringkat1->statements(),
            'kenyataan' => null,
            'signed_at' => now(),
        ];
    }

    public function stage(SignatureStage $stage): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => $stage,
            'statements' => $stage->statements(),
        ]);
    }
}
