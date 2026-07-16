<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Permohonan;
use App\Models\User;

class PermohonanPolicy
{
    /**
     * Owning-organisation users, or any internal staff, may view a Permohonan.
     */
    public function view(User $user, Permohonan $permohonan): bool
    {
        return $this->belongsToOrganisation($user, $permohonan) || $this->isStaff($user);
    }

    /**
     * Only owning-organisation users may edit, and only while it is a draft.
     */
    public function update(User $user, Permohonan $permohonan): bool
    {
        return $this->belongsToOrganisation($user, $permohonan) && $permohonan->status->isDraf();
    }

    /**
     * Only owning-organisation users may submit a draft.
     */
    public function submit(User $user, Permohonan $permohonan): bool
    {
        return $this->update($user, $permohonan);
    }

    /**
     * Uploading supporting documents follows the same rule as editing.
     */
    public function uploadDocument(User $user, Permohonan $permohonan): bool
    {
        return $this->update($user, $permohonan);
    }

    private function belongsToOrganisation(User $user, Permohonan $permohonan): bool
    {
        return $user->pemohon_id !== null && $user->pemohon_id === $permohonan->pemohon_id;
    }

    private function isStaff(User $user): bool
    {
        return $user->role !== null && $user->role !== UserRole::Pemohon;
    }
}
