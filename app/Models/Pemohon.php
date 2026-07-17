<?php

namespace App\Models;

use App\Enums\PemohonStatus;
use App\Enums\SumberDana;
use Database\Factories\PemohonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A Pemohon organisation (government agency / GLC) applying for a loan.
 * Its account is provisioned by SID, may have several users, and may hold
 * several Permohonan over time. The funding-source routing determinants
 * (Sumber Dana and its dependent controlling-ministry fields) are set by SID at
 * provisioning and inherited by every Permohonan the organisation creates
 * (ticket 16, ADR-0004).
 *
 * @property int $id
 * @property string $nama
 * @property PemohonStatus $status
 * @property SumberDana|null $sumber_dana
 * @property bool|null $ada_kementerian_pengawal
 * @property int|null $kementerian_pengawal_id
 * @property-read KementerianPengawal|null $kementerianPengawal
 */
class Pemohon extends Model
{
    /** @use HasFactory<PemohonFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'status',
        'sumber_dana',
        'ada_kementerian_pengawal',
        'kementerian_pengawal_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PemohonStatus::class,
            'sumber_dana' => SumberDana::class,
            'ada_kementerian_pengawal' => 'boolean',
        ];
    }

    /**
     * The controlling ministry set for this organisation's routing, if any.
     *
     * @return BelongsTo<KementerianPengawal, $this>
     */
    public function kementerianPengawal(): BelongsTo
    {
        return $this->belongsTo(KementerianPengawal::class);
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * A Pemohon organisation may hold several Permohonan over time.
     *
     * @return HasMany<Permohonan, $this>
     */
    public function permohonans(): HasMany
    {
        return $this->hasMany(Permohonan::class);
    }

    public function isPeminjam(): bool
    {
        return $this->status === PemohonStatus::Peminjam;
    }
}
