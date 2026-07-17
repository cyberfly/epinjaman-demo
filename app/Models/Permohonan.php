<?php

namespace App\Models;

use App\Enums\KuiriStatus;
use App\Enums\PermohonanStatus;
use App\Enums\SumberDana;
use App\Enums\TrafficLight;
use Database\Factories\PermohonanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * The loan application aggregate root.
 *
 * @property int $id
 * @property int $pemohon_id
 * @property string|null $no_rujukan
 * @property PermohonanStatus $status
 * @property TrafficLight|null $traffic_light
 * @property string|null $tajuk
 * @property string|null $tujuan
 * @property numeric-string|null $jumlah_dipohon
 * @property int|null $tempoh_bulan
 * @property SumberDana|null $sumber_dana
 * @property bool|null $ada_kementerian_pengawal
 * @property int|null $kementerian_pengawal_id
 * @property Carbon|null $dihantar_pada
 * @property Carbon|null $dihantar_ke_kementerian_pada
 * @property Carbon|null $diterima_sid_pada
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
        'dihantar_ke_kementerian_pada',
        'diterima_sid_pada',
        'traffic_light',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PermohonanStatus::class,
            'traffic_light' => TrafficLight::class,
            'sumber_dana' => SumberDana::class,
            'ada_kementerian_pengawal' => 'boolean',
            'jumlah_dipohon' => 'decimal:2',
            'tempoh_bulan' => 'integer',
            'dihantar_pada' => 'datetime',
            'dihantar_ke_kementerian_pada' => 'datetime',
            'diterima_sid_pada' => 'datetime',
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
     * @return HasMany<DigitalSignature, $this>
     */
    public function signatures(): HasMany
    {
        return $this->hasMany(DigitalSignature::class);
    }

    /**
     * @return HasMany<DocumentReview, $this>
     */
    public function documentReviews(): HasMany
    {
        return $this->hasMany(DocumentReview::class);
    }

    /**
     * @return HasMany<Kuiri, $this>
     */
    public function kuiris(): HasMany
    {
        return $this->hasMany(Kuiri::class);
    }

    /**
     * @return HasMany<Rundingan, $this>
     */
    public function rundingans(): HasMany
    {
        return $this->hasMany(Rundingan::class);
    }

    /**
     * @return HasOne<Memo, $this>
     */
    public function memo(): HasOne
    {
        return $this->hasOne(Memo::class)->latestOfMany();
    }

    /**
     * Whether every Kuiri raised on this application is Berpuas Hati and at
     * least one Kuiri exists (ticket 07 gate to Rundingan).
     */
    public function semuaKuiriBerpuasHati(): bool
    {
        return $this->kuiris()->exists()
            && ! $this->kuiris()->where('status', '!=', KuiriStatus::BerpuasHati->value)->exists();
    }

    /**
     * Whether a controlling ministry is attached, and therefore stage-2 (KP
     * signature) applies after stage-1 (see the process chart D -> F -> H).
     */
    public function hasKementerianPengawal(): bool
    {
        return $this->kementerian_pengawal_id !== null;
    }

    /**
     * KWAPBB where the applicant answered "no controlling ministry" skips the
     * completeness check and stage-1 signature entirely, going straight to SID
     * (ADR-0002). DE and KWAPBB-with-ministry both take the normal path.
     */
    public function skipsCompletenessAndStageOne(): bool
    {
        return $this->sumber_dana === SumberDana::KWAPBB && $this->ada_kementerian_pengawal !== true;
    }

    public function isLocked(): bool
    {
        return $this->status->isLocked();
    }
}
