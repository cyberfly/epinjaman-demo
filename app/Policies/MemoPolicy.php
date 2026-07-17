<?php

namespace App\Policies;

use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Memo;
use App\Models\User;

class MemoPolicy
{
    /**
     * Endorse & send to the next level — the current-level officer, not YB MK.
     */
    public function endorse(User $user, Memo $memo): bool
    {
        return $this->atCurrentLevel($user, $memo)
            && $memo->peringkat->nextInHierarchy() !== null;
    }

    /**
     * Return to the previous level — the current-level officer, not PSID.
     */
    public function returnToPrevious(User $user, Memo $memo): bool
    {
        return $this->atCurrentLevel($user, $memo)
            && $memo->peringkat->previousInHierarchy() !== null;
    }

    /**
     * Only YB MK, when the memo has reached the YB MK level.
     */
    public function decide(User $user, Memo $memo): bool
    {
        return $this->isPending($memo)
            && $user->role === UserRole::YBMK
            && $memo->peringkat === UserRole::YBMK;
    }

    /**
     * SID / audit / any internal officer may view the movement history.
     */
    public function viewHistory(User $user, Memo $memo): bool
    {
        return $user->role !== null && $user->role !== UserRole::Pemohon;
    }

    private function atCurrentLevel(User $user, Memo $memo): bool
    {
        return $this->isPending($memo) && $user->role === $memo->peringkat;
    }

    private function isPending(Memo $memo): bool
    {
        return $memo->permohonan->status === PermohonanStatus::DalamKelulusan
            && $memo->keputusan === null;
    }
}
