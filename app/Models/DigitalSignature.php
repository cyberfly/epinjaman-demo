<?php

namespace App\Models;

use App\Enums\SignatureStage;
use App\Enums\UserRole;
use Database\Factories\DigitalSignatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property int $user_id
 * @property UserRole $role
 * @property SignatureStage $stage
 * @property array<int, string> $statements
 * @property string|null $kenyataan
 * @property Carbon $signed_at
 */
class DigitalSignature extends Model
{
    /** @use HasFactory<DigitalSignatureFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'user_id',
        'role',
        'stage',
        'statements',
        'kenyataan',
        'signed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'stage' => SignatureStage::class,
            'statements' => 'array',
            'signed_at' => 'datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
