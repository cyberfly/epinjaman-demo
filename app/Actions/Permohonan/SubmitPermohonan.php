<?php

namespace App\Actions\Permohonan;

use App\Enums\PermohonanStatus;
use App\Enums\SumberDana;
use App\Models\Permohonan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Submit a draft Permohonan. Validates that the mandatory fields are present,
 * then routes the application to the correct post-submission status per the
 * funding-source branch (ADR-0002):
 *
 *  - DE, or KWAPBB with a controlling ministry -> waiting completeness check &
 *    stage-1 signature.
 *  - KWAPBB without a controlling ministry -> straight to SID (skips both).
 */
class SubmitPermohonan
{
    public function handle(Permohonan $permohonan): Permohonan
    {
        $this->validateRequiredFields($permohonan);

        $status = $permohonan->skipsCompletenessAndStageOne()
            ? PermohonanStatus::DalamSemakanSID
            : PermohonanStatus::MenungguSemakanKelengkapan;

        $permohonan->update([
            'status' => $status,
            'dihantar_pada' => now(),
        ]);

        return $permohonan;
    }

    /**
     * @throws ValidationException
     */
    private function validateRequiredFields(Permohonan $permohonan): void
    {
        $isKwapbb = $permohonan->sumber_dana === SumberDana::KWAPBB;
        $hasMinistry = $permohonan->ada_kementerian_pengawal === true;

        Validator::make($permohonan->only([
            'tajuk',
            'jumlah_dipohon',
            'tujuan',
            'tempoh_bulan',
            'sumber_dana',
            'ada_kementerian_pengawal',
            'kementerian_pengawal_id',
        ]), [
            'tajuk' => ['required', 'string'],
            'jumlah_dipohon' => ['required', 'numeric', 'min:0'],
            'tujuan' => ['required', 'string'],
            'tempoh_bulan' => ['required', 'integer', 'min:1'],
            'sumber_dana' => ['required'],
            'ada_kementerian_pengawal' => [Rule::requiredIf($isKwapbb)],
            'kementerian_pengawal_id' => [Rule::requiredIf($isKwapbb && $hasMinistry)],
        ])->validate();
    }
}
