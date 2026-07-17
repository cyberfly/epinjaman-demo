<?php

namespace App\Models;

use App\Enums\ApprovalStepAction;
use App\Enums\UserRole;
use Database\Factories\ApprovalStepFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $memo_id
 * @property int|null $user_id
 * @property UserRole $peringkat
 * @property ApprovalStepAction $tindakan
 * @property string|null $sebab
 */
class ApprovalStep extends Model
{
    /** @use HasFactory<ApprovalStepFactory> */
    use HasFactory;

    protected $fillable = [
        'memo_id',
        'user_id',
        'peringkat',
        'tindakan',
        'sebab',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'peringkat' => UserRole::class,
            'tindakan' => ApprovalStepAction::class,
        ];
    }

    /**
     * @return BelongsTo<Memo, $this>
     */
    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
