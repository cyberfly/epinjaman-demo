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

    /**
     * Only PSID may review the checklist while the application is in SID review.
     */
    public function reviewDocuments(User $user, Permohonan $permohonan): bool
    {
        return $user->role === UserRole::PSID
            && $permohonan->status === PermohonanStatus::DalamSemakanSID;
    }

    /**
     * Only PSID may raise and resolve Kuiri, during SID review / Kuiri stages.
     */
    public function manageKuiri(User $user, Permohonan $permohonan): bool
    {
        return $user->role === UserRole::PSID
            && in_array($permohonan->status, [PermohonanStatus::DalamSemakanSID, PermohonanStatus::DalamKuiri], true);
    }

    /**
     * The owning organisation or the controlling ministry may reply to Kuiri.
     */
    public function replyKuiri(User $user, Permohonan $permohonan): bool
    {
        if ($permohonan->status !== PermohonanStatus::DalamKuiri) {
            return false;
        }

        return $this->belongsToOrganisation($user, $permohonan) || $this->belongsToControllingMinistry($user, $permohonan);
    }

    /**
     * The owning organisation signs the Surat Akuan Penerimaan while offered.
     */
    public function signSuratAkuan(User $user, Permohonan $permohonan): bool
    {
        return $this->belongsToOrganisation($user, $permohonan)
            && $permohonan->status === PermohonanStatus::Ditawarkan;
    }

    /**
     * The owning organisation uploads the draft agreement while in the
     * agreement-preparation stage.
     */
    public function uploadDrafPerjanjian(User $user, Permohonan $permohonan): bool
    {
        return $this->belongsToOrganisation($user, $permohonan)
            && $permohonan->status === PermohonanStatus::DalamPerjanjian;
    }

    /**
     * SID and BUU review the draft agreement.
     */
    public function reviewPerjanjian(User $user, Permohonan $permohonan): bool
    {
        return ($user->isSid() || $user->role === UserRole::BUU)
            && $permohonan->status === PermohonanStatus::DalamPerjanjian;
    }

    /**
     * Only BUU confirms the draft agreement is orderly.
     */
    public function approvePerjanjian(User $user, Permohonan $permohonan): bool
    {
        return $user->role === UserRole::BUU
            && $permohonan->status === PermohonanStatus::DalamPerjanjian;
    }

    /**
     * SID records the manual signing session.
     */
    public function recordTandatanganManual(User $user, Permohonan $permohonan): bool
    {
        return $user->isSid() && $permohonan->status === PermohonanStatus::DalamPerjanjian;
    }

    /**
     * The Peminjam organisation, or SID on its behalf, records LHDNM stamping.
     */
    public function recordPenyeteman(User $user, Permohonan $permohonan): bool
    {
        return ($user->isSid() || $this->belongsToOrganisation($user, $permohonan))
            && $permohonan->status === PermohonanStatus::DalamPerjanjian;
    }

    /**
     * The Peminjam organisation (or SID) uploads CP documents.
     */
    public function uploadSyaratDuluan(User $user, Permohonan $permohonan): bool
    {
        return ($user->isSid() || $this->belongsToOrganisation($user, $permohonan))
            && $permohonan->status === PermohonanStatus::DalamPenyediaanCP;
    }

    /**
     * PSID verifies CP items, confirms CS, and locks the application LENGKAP.
     */
    public function manageSyaratDuluan(User $user, Permohonan $permohonan): bool
    {
        return $user->role === UserRole::PSID
            && $permohonan->status === PermohonanStatus::DalamPenyediaanCP;
    }

    /**
     * SID / audit / any internal officer may view the full audit history.
     */
    public function viewHistory(User $user, Permohonan $permohonan): bool
    {
        return $user->role !== null && $user->role !== UserRole::Pemohon;
    }

    /**
     * SID or the owning organisation may record negotiation terms while in
     * the Rundingan stage.
     */
    public function recordRundingan(User $user, Permohonan $permohonan): bool
    {
        return $permohonan->status === PermohonanStatus::DalamRundingan
            && ($user->isSid() || $this->belongsToOrganisation($user, $permohonan));
    }

    /**
     * Only PSID may prepare the Memo Pertimbangan, in the Rundingan stage.
     */
    public function prepareMemo(User $user, Permohonan $permohonan): bool
    {
        return $user->role === UserRole::PSID
            && $permohonan->status === PermohonanStatus::DalamRundingan;
    }

    private function belongsToControllingMinistry(User $user, Permohonan $permohonan): bool
    {
        return $user->role === UserRole::KementerianPengawal
            && $user->kementerian_pengawal_id !== null
            && $user->kementerian_pengawal_id === $permohonan->kementerian_pengawal_id;
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
