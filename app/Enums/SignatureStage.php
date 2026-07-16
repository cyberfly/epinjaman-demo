<?php

namespace App\Enums;

/**
 * A point in the lifecycle where a Digital Signature is applied. Recorded as an
 * internal audited action (see ADR-0001) — not a third-party e-signature.
 */
enum SignatureStage: string
{
    case Peringkat1 = 'peringkat_1';
    case Peringkat2 = 'peringkat_2';
    case Penerimaan = 'penerimaan';

    public function label(): string
    {
        return match ($this) {
            self::Peringkat1 => 'Tandatangan Digital Peringkat 1 (Pemohon)',
            self::Peringkat2 => 'Tandatangan Digital Peringkat 2 (Kementerian Pengawal)',
            self::Penerimaan => 'Tandatangan Surat Akuan Penerimaan',
        };
    }

    /**
     * The affirmation statements applied at this stage.
     *
     * @return array<int, string>
     */
    public function statements(): array
    {
        return match ($this) {
            self::Peringkat1 => ['Disediakan', 'Disahkan'],
            self::Peringkat2 => ['Disemak', 'Diperaku'],
            self::Penerimaan => ['Setuju Terima'],
        };
    }
}
