<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\KuiriReplyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $kuiri_id
 * @property int|null $user_id
 * @property UserRole|null $role
 * @property string|null $mesej
 * @property string|null $lampiran_path
 * @property string|null $lampiran_nama
 */
class KuiriReply extends Model
{
    /** @use HasFactory<KuiriReplyFactory> */
    use HasFactory;

    protected $fillable = [
        'kuiri_id',
        'user_id',
        'role',
        'mesej',
        'lampiran_path',
        'lampiran_nama',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
        ];
    }

    /**
     * @return BelongsTo<Kuiri, $this>
     */
    public function kuiri(): BelongsTo
    {
        return $this->belongsTo(Kuiri::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
