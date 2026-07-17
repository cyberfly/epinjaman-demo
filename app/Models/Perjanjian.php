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
