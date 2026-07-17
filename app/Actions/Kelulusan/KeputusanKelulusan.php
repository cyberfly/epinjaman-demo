<?php

namespace App\Actions\Kelulusan;

use App\Enums\ApprovalDecision;
use App\Enums\ApprovalStepAction;
use App\Enums\PermohonanStatus;
use App\Enums\UserRole;
use App\Models\Memo;
use App\Models\User;
use DomainException;

/**
 * YB MK makes the final Approval Decision (Lulus / Tidak Lulus) on a memo that
 * has reached the YB MK level (ticket 09).
 *
 *  - Tidak Lulus -> the application returns to Rundingan (ticket 08).
 *  - Lulus -> the application is ready for offer-letter generation (ticket 10).
 */
class KeputusanKelulusan
{
    public function handle(Memo $memo, User $user, ApprovalDecision $keputusan, ?string $sebab = null): Memo
    {
        if ($memo->permohonan->status !== PermohonanStatus::DalamKelulusan || $memo->keputusan !== null) {
            throw new DomainException('Memo tidak berada pada peringkat kelulusan.');
        }

        if ($user->role !== UserRole::YBMK || $memo->peringkat !== UserRole::YBMK) {
            throw new DomainException('Hanya YB MK boleh membuat keputusan kelulusan.');
        }

        $memo->update(['keputusan' => $keputusan]);

        $memo->approvalSteps()->create([
            'user_id' => $user->getKey(),
            'peringkat' => UserRole::YBMK,
            'tindakan' => $keputusan === ApprovalDecision::Lulus ? ApprovalStepAction::Lulus : ApprovalStepAction::TidakLulus,
            'sebab' => $sebab,
        ]);

        if ($keputusan === ApprovalDecision::TidakLulus) {
            $memo->permohonan->update([
                'status' => PermohonanStatus::DalamRundingan,
                'traffic_light' => null,
            ]);
        }

        return $memo;
    }
}
