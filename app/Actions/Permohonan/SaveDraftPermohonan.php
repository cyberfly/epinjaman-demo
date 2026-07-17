<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Pemohon;
use App\Models\Permohonan;
use Illuminate\Support\Arr;

/**
 * Create or update a draft Permohonan for a Pemohon organisation. Only the
 * loan-detail form fields are written from the request; the funding-source
 * routing determinants are never accepted from the Pemohon — a new draft
 * inherits them from the organisation (ticket 16, ADR-0004).
 */
class SaveDraftPermohonan
{
    private const FILLABLE = [
        'tajuk',
        'jumlah_dipohon',
        'tujuan',
        'tempoh_bulan',
    ];

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Pemohon $pemohon, array $data, ?Permohonan $permohonan = null): Permohonan
    {
        $attributes = Arr::only($data, self::FILLABLE);

        if ($permohonan !== null) {
            $permohonan->update($attributes);

            return $permohonan;
        }

        return $pemohon->permohonans()->create($attributes + [
            'status' => PermohonanStatus::Draf,
            'sumber_dana' => $pemohon->sumber_dana,
            'ada_kementerian_pengawal' => $pemohon->ada_kementerian_pengawal,
            'kementerian_pengawal_id' => $pemohon->kementerian_pengawal_id,
        ]);
    }
}
