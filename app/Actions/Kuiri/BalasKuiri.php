<?php

namespace App\Actions\Kuiri;

use App\Models\Kuiri;
use App\Models\KuiriReply;
use App\Models\User;
use DomainException;
use Illuminate\Validation\ValidationException;

/**
 * Pemohon or Kementerian Pengawal officer replies to a Kuiri item with a
 * message and/or an uploaded document (ticket 07).
 */
class BalasKuiri
{
    public function handle(
        Kuiri $kuiri,
        User $user,
        ?string $mesej = null,
        ?string $lampiranPath = null,
        ?string $lampiranNama = null,
    ): KuiriReply {
        if ($kuiri->isSatisfied()) {
            throw new DomainException('Kuiri telah selesai dan tidak boleh dibalas.');
        }

        if (blank($mesej) && $lampiranPath === null) {
            throw ValidationException::withMessages([
                'mesej' => 'Sila berikan mesej atau muat naik dokumen.',
            ]);
        }

        return $kuiri->replies()->create([
            'user_id' => $user->getKey(),
            'role' => $user->role,
            'mesej' => $mesej,
            'lampiran_path' => $lampiranPath,
            'lampiran_nama' => $lampiranNama,
        ]);
    }
}
