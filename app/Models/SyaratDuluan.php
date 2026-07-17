<?php

namespace App\Models;

use App\Enums\SyaratDuluanJenis;
use Database\Factories\SyaratDuluanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property SyaratDuluanJenis $jenis
 * @property string|null $dokumen_path
 * @property string|null $dokumen_nama
 * @property int|null $dimuat_naik_oleh
 * @property bool $disahkan
 * @property int|null $disahkan_oleh
 * @property Carbon|null $disahkan_pada
 */
class SyaratDuluan extends Model
{
    /** @use HasFactory<SyaratDuluanFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'jenis',
        'dokumen_path',
        'dokumen_nama',
        'dimuat_naik_oleh',
        'disahkan',
        'disahkan_oleh',
        'disahkan_pada',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis' => SyaratDuluanJenis::class,
            'disahkan' => 'boolean',
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
}
