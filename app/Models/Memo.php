<?php

namespace App\Models;

use App\Enums\ApprovalDecision;
use App\Enums\UserRole;
use Database\Factories\MemoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property int|null $disediakan_oleh
 * @property string $terma_utama
 * @property string|null $catatan
 * @property UserRole $peringkat
 * @property ApprovalDecision|null $keputusan
 * @property Carbon|null $disediakan_pada
 */
class Memo extends Model
{
    /** @use HasFactory<MemoFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'disediakan_oleh',
        'terma_utama',
        'catatan',
        'peringkat',
        'keputusan',
        'disediakan_pada',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'peringkat' => UserRole::class,
            'keputusan' => ApprovalDecision::class,
            'disediakan_pada' => 'datetime',
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
     * @return HasMany<ApprovalStep, $this>
     */
    public function approvalSteps(): HasMany
    {
        return $this->hasMany(ApprovalStep::class);
    }
}
