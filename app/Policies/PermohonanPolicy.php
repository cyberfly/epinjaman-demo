<?php

namespace App\Policies;

use App\Enums\PermohonanStatus;
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

    /**
     * Only owning-organisation users may apply the stage-1 signature, and only
     * while the application is awaiting the completeness check & stage-1.
     */
    public function signStagePertama(User $user, Permohonan $permohonan): bool
    {
        return $this->belongsToOrganisation($user, $permohonan)
            && $permohonan->status === PermohonanStatus::MenungguSemakanKelengkapan;
    }

    /**
     * Only an officer of the application's controlling ministry may apply the
     * stage-2 signature, while it awaits the ministry.
     */
    public function signStageKedua(User $user, Permohonan $permohonan): bool
    {
        return $user->role === UserRole::KementerianPengawal
            && $user->kementerian_pengawal_id !== null
            && $user->kementerian_pengawal_id === $permohonan->kementerian_pengawal_id
            && $permohonan->status === PermohonanStatus::MenungguTandatanganKementerianPengawal;
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
