<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Perjanjian;
use App\Models\PerjanjianUlasan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PerjanjianUlasan>
 */
class PerjanjianUlasanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'perjanjian_id' => Perjanjian::factory(),
            'user_id' => null,
            'role' => UserRole::BUU,
            'ulasan' => fake()->sentence(),
        ];
    }
}
