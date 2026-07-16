<?php

namespace App\Models;

use App\Enums\PermohonanStatus;
use App\Enums\SumberDana;
use Database\Factories\PermohonanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * The loan application aggregate root.
 *
 * @property int $id
 * @property int $pemohon_id
 * @property string|null $no_rujukan
 * @property PermohonanStatus $status
 * @property string|null $tajuk
 * @property string|null $tujuan
 * @property numeric-string|null $jumlah_dipohon
 * @property int|null $tempoh_bulan
 * @property SumberDana|null $sumber_dana
 * @property bool|null $ada_kementerian_pengawal
 * @property int|null $kementerian_pengawal_id
 * @property Carbon|null $dihantar_pada
 * @property-read Pemohon $pemohon
 * @property-read KementerianPengawal|null $kementerianPengawal
 */
class Permohonan extends Model
{
    /** @use HasFactory<PermohonanFactory> */
    use HasFactory;

    protected $fillable = [
        'pemohon_id',
        'no_rujukan',
        'status',
        'tajuk',
        'jumlah_dipohon',
        'tujuan',
        'tempoh_bulan',
        'sumber_dana',
        'ada_kementerian_pengawal',
        'kementerian_pengawal_id',
        'dihantar_pada',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PermohonanStatus::class,
            'sumber_dana' => SumberDana::class,
            'ada_kementerian_pengawal' => 'boolean',
            'jumlah_dipohon' => 'decimal:2',
            'tempoh_bulan' => 'integer',
            'dihantar_pada' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Pemohon, $this>
     */
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class);
    }

    /**
     * @return BelongsTo<KementerianPengawal, $this>
     */
    public function kementerianPengawal(): BelongsTo
    {
        return $this->belongsTo(KementerianPengawal::class);
    }

    /**
     * @return HasMany<PermohonanDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PermohonanDocument::class);
    }

    /**
     * Whether a controlling ministry applies to this application (only relevant
     * for KWAPBB-sourced applications — see ADR-0002).
     */
    public function hasControllingMinistry(): bool
    {
        return $this->ada_kementerian_pengawal === true && $this->kementerian_pengawal_id !== null;
    }

    /**
     * KWAPBB without a controlling ministry skips the completeness check and
     * stage-1 signature entirely (ADR-0002).
     */
    public function skipsCompletenessAndStageOne(): bool
    {
        return $this->sumber_dana === SumberDana::KWAPBB && ! $this->hasControllingMinistry();
    }

    public function isLocked(): bool
    {
        return $this->status->isLocked();
    }
}
