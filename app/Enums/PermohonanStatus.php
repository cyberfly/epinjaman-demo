<?php

namespace App\Enums;

/**
 * Coarse phase status of a Permohonan across the full lifecycle. The precise
 * node (current hierarchy step, who must act) is tracked in separate step
 * structures, not encoded into this enum (see spec Implementation Decisions).
 */
enum PermohonanStatus: string
{
    case Draf = 'draf';
    case MenungguSemakanKelengkapan = 'menunggu_semakan_kelengkapan';
    case MenungguTandatanganKementerianPengawal = 'menunggu_tandatangan_kementerian_pengawal';
    case DalamSemakanSID = 'dalam_semakan_sid';
    case DalamKuiri = 'dalam_kuiri';
    case DalamRundingan = 'dalam_rundingan';
    case DalamKelulusan = 'dalam_kelulusan';
    case Ditawarkan = 'ditawarkan';
    case DalamPerjanjian = 'dalam_perjanjian';
    case DalamPenyediaanCP = 'dalam_penyediaan_cp';
    case Lengkap = 'lengkap';

    public function label(): string
    {
        return match ($this) {
            self::Draf => 'Draf',
            self::MenungguSemakanKelengkapan => 'Menunggu Semakan Kelengkapan & Tandatangan Peringkat 1',
            self::MenungguTandatanganKementerianPengawal => 'Menunggu Tandatangan Kementerian Pengawal',
            self::DalamSemakanSID => 'Dalam Proses Semakan (SID)',
            self::DalamKuiri => 'Dalam Kuiri',
            self::DalamRundingan => 'Dalam Rundingan',
            self::DalamKelulusan => 'Dalam Kelulusan',
            self::Ditawarkan => 'Ditawarkan',
            self::DalamPerjanjian => 'Dalam Penyediaan Perjanjian',
            self::DalamPenyediaanCP => 'Dalam Penyediaan Syarat Duluan',
            self::Lengkap => 'Lengkap',
        };
    }

    /**
     * A draft is the only status the Pemohon may freely edit.
     */
    public function isDraf(): bool
    {
        return $this === self::Draf;
    }

    /**
     * Once LENGKAP the record is read-only (see ticket 13).
     */
    public function isLocked(): bool
    {
        return $this === self::Lengkap;
    }
}
