<?php

namespace App\Actions\Account;

use App\Models\User;

/**
 * Activate a SID-provisioned account: set the chosen password, mark the email
 * verified, and stamp the activation time.
 */
class ActivateAccount
{
    public function handle(User $user, string $password): User
    {
        $user->forceFill([
            'password' => $password,
            'email_verified_at' => now(),
            'activated_at' => now(),
        ])->save();

        return $user;
    }

    public function alreadyActivated(User $user): bool
    {
        return $user->activated_at !== null;
    }
}
