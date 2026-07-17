<?php

namespace App\Models;

use App\Enums\KuiriStatus;
use Database\Factories\KuiriFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property int|null $dicetus_oleh
 * @property string $tajuk
 * @property string $sebab
 * @property KuiriStatus $status
 * @property Carbon|null $tarikh_akhir
 */
class Kuiri extends Model
{
    /** @use HasFactory<KuiriFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'dicetus_oleh',
        'tajuk',
        'sebab',
        'status',
        'tarikh_akhir',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => KuiriStatus::class,
            'tarikh_akhir' => 'datetime',
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
     * @return HasMany<KuiriReply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(KuiriReply::class);
    }

    public function isSatisfied(): bool
    {
        return $this->status->isSatisfied();
    }
}
