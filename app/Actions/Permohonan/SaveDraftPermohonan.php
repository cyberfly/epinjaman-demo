<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Models\Pemohon;
use App\Models\Permohonan;
use Illuminate\Support\Arr;

/**
 * Create or update a draft Permohonan for a Pemohon organisation. Only the
 * structured form fields are written; status/routing is decided at submission.
 */
class SaveDraftPermohonan
{
    private const FILLABLE = [
        'tajuk',
        'jumlah_dipohon',
        'tujuan',
        'tempoh_bulan',
        'sumber_dana',
        'ada_kementerian_pengawal',
        'kementerian_pengawal_id',
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
        ]);
    }
}
