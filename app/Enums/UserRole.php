<?php

namespace App\Enums;

/**
 * All roles across the ePinjaman loan lifecycle.
 *
 * Value strings are stored on the `users.role` column. Labels are the
 * domain terms used across the reference charts (Bahasa Melayu).
 */
enum UserRole: string
{
    case Pemohon = 'pemohon';
    case KementerianPengawal = 'kementerian_pengawal';
    case PSID = 'psid';
    case KSID = 'ksid';
    case KKID = 'kkid';
    case TSID = 'tsid';
    case SSID = 'ssid';
    case TKSPI = 'tkspi';
    case KSP = 'ksp';
    case YBMK = 'yb_mk';
    case BUU = 'buu';
    case Admin = 'admin';

    /**
     * Human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pemohon => 'Pemohon',
            self::KementerianPengawal => 'Kementerian Pengawal',
            self::PSID => 'Pegawai SID (PSID)',
            self::KSID => 'Ketua Cawangan SID (KSID)',
            self::KKID => 'Ketua Bahagian SID (KKID)',
            self::TSID => 'Timbalan Ketua Pengarah SID (TSID)',
            self::SSID => 'Ketua Pengarah SID (SSID)',
            self::TKSPI => 'Timbalan KSP Pelaburan (TKSP(I))',
            self::KSP => 'Ketua Setiausaha Perbendaharaan (KSP)',
            self::YBMK => 'Menteri Kewangan (YB MK)',
            self::BUU => 'Bahagian Undang-Undang (BUU)',
            self::Admin => 'Pentadbir Sistem',
        };
    }

    /**
     * The ordered approval hierarchy the Memo Pertimbangan travels through.
     *
     * @return array<int, self>
     */
    public static function approvalHierarchy(): array
    {
        return [
            self::PSID,
            self::KSID,
            self::KKID,
            self::TSID,
            self::SSID,
            self::TKSPI,
            self::KSP,
            self::YBMK,
        ];
    }

    /**
     * Roles that form the internal SID processing/management chain.
     *
     * @return array<int, self>
     */
    public static function sidRoles(): array
    {
        return [self::PSID, self::KSID, self::KKID, self::TSID, self::SSID];
    }

    /**
     * The next level in the approval hierarchy, or null if this is the last
     * (YB MK) or not part of the hierarchy.
     */
    public function nextInHierarchy(): ?self
    {
        $hierarchy = self::approvalHierarchy();
        $index = array_search($this, $hierarchy, true);

        return $index === false ? null : ($hierarchy[$index + 1] ?? null);
    }

    /**
     * The previous level in the approval hierarchy, or null if this is the
     * first (PSID) or not part of the hierarchy.
     */
    public function previousInHierarchy(): ?self
    {
        $hierarchy = self::approvalHierarchy();
        $index = array_search($this, $hierarchy, true);

        return $index === false || $index === 0 ? null : $hierarchy[$index - 1];
    }

    /**
     * Whether this role may provision Pemohon organisations and users.
     */
    public function canProvisionAccounts(): bool
    {
        return $this === self::Admin || in_array($this, self::sidRoles(), true);
    }
}
