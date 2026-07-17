<?php

namespace Database\Factories;

use App\Enums\ApprovalStepAction;
use App\Enums\UserRole;
use App\Models\ApprovalStep;
use App\Models\Memo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApprovalStep>
 */
class ApprovalStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'memo_id' => Memo::factory(),
            'user_id' => null,
            'peringkat' => UserRole::PSID,
            'tindakan' => ApprovalStepAction::Endorse,
            'sebab' => null,
        ];
    }
}
