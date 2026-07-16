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
    public function __construct(private ReceiveAtSid $receiveAtSid) {}

    public function handle(Permohonan $permohonan): Permohonan
    {
        $this->validateRequiredFields($permohonan);

        $permohonan->update(['dihantar_pada' => now()]);

        // KWAPBB without a controlling ministry goes straight to SID, skipping
        // the completeness check and stage-1 signature (ADR-0002).
        if ($permohonan->skipsCompletenessAndStageOne()) {
            return $this->receiveAtSid->handle($permohonan);
        }

        $permohonan->update(['status' => PermohonanStatus::MenungguSemakanKelengkapan]);

        return $permohonan;
    }

    /**
     * @throws ValidationException
     */
    private function validateRequiredFields(Permohonan $permohonan): void
    {
        $isKwapbb = $permohonan->sumber_dana === SumberDana::KWAPBB;

        // A controlling ministry is required for every path that reaches
        // stage-1/stage-2 (DE, and KWAPBB-with-ministry) — only KWAPBB without
        // a ministry skips it (ADR-0002).
        $requiresMinistry = ! $permohonan->skipsCompletenessAndStageOne();

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
            'kementerian_pengawal_id' => [Rule::requiredIf($requiresMinistry)],
        ])->validate();
    }
}
