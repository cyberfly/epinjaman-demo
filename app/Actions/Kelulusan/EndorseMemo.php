<?php

namespace App\Actions\Kelulusan;

use App\Enums\ApprovalStepAction;
use App\Enums\PermohonanStatus;
use App\Models\Memo;
use App\Models\User;
use DomainException;

/**
 * The officer at the memo's current level endorses it and sends it to the next
 * level in the approval hierarchy (ticket 09).
 */
class EndorseMemo
{
    public function handle(Memo $memo, User $user): Memo
    {
        $this->guard($memo, $user);

        $next = $memo->peringkat->nextInHierarchy();

        if ($next === null) {
            throw new DomainException('Peringkat akhir membuat keputusan, bukan endorse.');
        }

        $memo->approvalSteps()->create([
            'user_id' => $user->getKey(),
            'peringkat' => $memo->peringkat,
            'tindakan' => ApprovalStepAction::Endorse,
        ]);

        $memo->update(['peringkat' => $next]);

        return $memo;
    }

    private function guard(Memo $memo, User $user): void
    {
        if ($memo->permohonan->status !== PermohonanStatus::DalamKelulusan || $memo->keputusan !== null) {
            throw new DomainException('Memo tidak berada pada peringkat kelulusan.');
        }

        if ($user->role !== $memo->peringkat) {
            throw new DomainException('Hanya pegawai peringkat semasa boleh bertindak.');
        }
    }
}
