<?php

namespace App\Actions\Pemohon;

use App\Enums\UserRole;
use App\Models\Pemohon;
use App\Models\User;
use App\Notifications\AccountActivationNotification;
use Illuminate\Support\Str;

/**
 * Provision a Pemohon user under an organisation. The account is created in an
 * inactive/unverified state with a random password and an activation email is
 * dispatched. Several users may be provisioned under the same organisation.
 */
class ProvisionPemohonUser
{
    public function handle(Pemohon $pemohon, string $name, string $email): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Str::password(32),
            'role' => UserRole::Pemohon,
            'pemohon_id' => $pemohon->getKey(),
        ]);

        $user->notify(new AccountActivationNotification);

        return $user;
    }
}
