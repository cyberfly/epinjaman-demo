<?php

namespace App\Actions\Pemohon;

use App\Enums\PemohonStatus;
use App\Models\Pemohon;

/**
 * Create a new Pemohon organisation. Accounts are provisioned by SID — never
 * through open self-registration.
 */
class CreatePemohonOrganisasi
{
    public function handle(string $nama): Pemohon
    {
        return Pemohon::create([
            'nama' => $nama,
            'status' => PemohonStatus::Pemohon,
        ]);
    }
}
