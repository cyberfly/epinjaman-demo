<?php

namespace App\Actions\Pemohon;

use App\Enums\PemohonStatus;
use App\Enums\SumberDana;
use App\Models\Pemohon;

/**
 * Create a new Pemohon organisation. Accounts are provisioned by SID — never
 * through open self-registration. SID also sets the funding-source routing
 * determinants here (Sumber Dana and its dependent controlling-ministry
 * fields); every Permohonan the organisation creates inherits them
 * (ticket 16, ADR-0004).
 */
class CreatePemohonOrganisasi
{
    public function handle(
        string $nama,
        ?SumberDana $sumberDana = null,
        ?bool $adaKementerianPengawal = null,
        ?int $kementerianPengawalId = null,
    ): Pemohon {
        return Pemohon::create([
            'nama' => $nama,
            'status' => PemohonStatus::Pemohon,
            'sumber_dana' => $sumberDana,
            'ada_kementerian_pengawal' => $adaKementerianPengawal,
            'kementerian_pengawal_id' => $kementerianPengawalId,
        ]);
    }
}
