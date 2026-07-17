<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Kuiri;
use App\Models\KuiriReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KuiriReply>
 */
class KuiriReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kuiri_id' => Kuiri::factory(),
            'user_id' => User::factory(),
            'role' => UserRole::Pemohon,
            'mesej' => fake()->sentence(),
            'lampiran_path' => null,
            'lampiran_nama' => null,
        ];
    }
}
