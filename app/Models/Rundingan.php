<?php

namespace App\Models;

use Database\Factories\RundinganFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property int|null $direkod_oleh
 * @property string $terma
 * @property string|null $catatan
 * @property bool $dipersetujui
 */
class Rundingan extends Model
{
    /** @use HasFactory<RundinganFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'direkod_oleh',
        'terma',
        'catatan',
        'dipersetujui',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dipersetujui' => 'boolean',
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
