<?php

namespace App\Actions\Kuiri;

use App\Enums\KuiriStatus;
use App\Models\Kuiri;

/**
 * PSID marks a Kuiri item as Berpuas Hati (closes it) or Tidak Berpuas Hati
 * (keeps it open for the next reply round) after reviewing the replies
 * (ticket 07).
 */
class TandakanKuiri
{
    public function berpuasHati(Kuiri $kuiri): Kuiri
    {
        $kuiri->update(['status' => KuiriStatus::BerpuasHati]);

        return $kuiri;
    }

    public function tidakBerpuasHati(Kuiri $kuiri): Kuiri
    {
        $kuiri->update(['status' => KuiriStatus::TidakBerpuasHati]);

        return $kuiri;
    }
}
