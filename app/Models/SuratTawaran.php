<?php

namespace App\Models;

use Database\Factories\SuratTawaranFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property string $kandungan
 * @property string $terma_utama
 * @property Carbon $dijana_pada
 */
class SuratTawaran extends Model
{
    /** @use HasFactory<SuratTawaranFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'kandungan',
        'terma_utama',
        'dijana_pada',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dijana_pada' => 'datetime',
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
