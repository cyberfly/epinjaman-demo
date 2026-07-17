<?php

namespace App\Models;

use Database\Factories\PerjanjianFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property string|null $draf_path
 * @property string|null $draf_nama
 * @property int|null $dimuat_naik_oleh
 * @property Carbon|null $dimuat_naik_pada
 * @property bool $disahkan_buu
 * @property int|null $disahkan_oleh
 * @property Carbon|null $disahkan_pada
 * @property bool $tandatangan_manual_selesai
 * @property Carbon|null $tandatangan_manual_tarikh
 * @property int|null $tandatangan_manual_oleh
 * @property string|null $tandatangan_manual_imbasan_path
 * @property bool $penyeteman_selesai
 * @property Carbon|null $penyeteman_tarikh
 * @property int|null $penyeteman_oleh
 */
class Perjanjian extends Model
{
    /** @use HasFactory<PerjanjianFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'draf_path',
        'draf_nama',
        'dimuat_naik_oleh',
        'dimuat_naik_pada',
        'disahkan_buu',
        'disahkan_oleh',
        'disahkan_pada',
        'tandatangan_manual_selesai',
        'tandatangan_manual_tarikh',
        'tandatangan_manual_oleh',
        'tandatangan_manual_imbasan_path',
        'penyeteman_selesai',
        'penyeteman_tarikh',
        'penyeteman_oleh',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dimuat_naik_pada' => 'datetime',
            'disahkan_buu' => 'boolean',
            'disahkan_pada' => 'datetime',
            'tandatangan_manual_selesai' => 'boolean',
            'tandatangan_manual_tarikh' => 'date',
            'penyeteman_selesai' => 'boolean',
            'penyeteman_tarikh' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Permohonan, $this>
     */
    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(Permohonan::class);
    }

    /**
     * @return HasMany<PerjanjianUlasan, $this>
     */
    public function ulasans(): HasMany
    {
        return $this->hasMany(PerjanjianUlasan::class);
    }
}
