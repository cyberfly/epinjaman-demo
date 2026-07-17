<?php

namespace App\Actions\Kelulusan;

use App\Enums\ApprovalStepAction;
use App\Enums\PermohonanStatus;
use App\Models\Memo;
use App\Models\User;
use App\Notifications\TemplatedNotification;
use DomainException;
use Illuminate\Support\Facades\Notification;

/**
 * The officer at the memo's current level returns it to the PREVIOUS level for
 * correction, with a reason (ADR-0003). Available at every level except the
 * first (PSID). The receiving level's officers are notified.
 */
class PulangkanMemo
{
    public function handle(Memo $memo, User $user, string $sebab): Memo
    {
        if ($memo->permohonan->status !== PermohonanStatus::DalamKelulusan || $memo->keputusan !== null) {
            throw new DomainException('Memo tidak berada pada peringkat kelulusan.');
        }

        if ($user->role !== $memo->peringkat) {
            throw new DomainException('Hanya pegawai peringkat semasa boleh bertindak.');
        }

        $previous = $memo->peringkat->previousInHierarchy();

        if ($previous === null) {
            throw new DomainException('Peringkat pertama tidak boleh memulangkan memo.');
        }

        $memo->approvalSteps()->create([
            'user_id' => $user->getKey(),
            'peringkat' => $memo->peringkat,
            'tindakan' => ApprovalStepAction::Pulangkan,
            'sebab' => $sebab,
        ]);

        $memo->update(['peringkat' => $previous]);

        $recipients = User::query()->where('role', $previous->value)->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new TemplatedNotification('memo.dipulangkan', [
                'no_rujukan' => $memo->permohonan->no_rujukan ?? (string) $memo->permohonan_id,
                'sebab' => $sebab,
            ]));
        }

        return $memo;
    }
}
