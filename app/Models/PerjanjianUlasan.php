<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\PerjanjianUlasanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $perjanjian_id
 * @property int|null $user_id
 * @property UserRole|null $role
 * @property string $ulasan
 */
class PerjanjianUlasan extends Model
{
    /** @use HasFactory<PerjanjianUlasanFactory> */
    use HasFactory;

    protected $fillable = [
        'perjanjian_id',
        'user_id',
        'role',
        'ulasan',
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
     * @return BelongsTo<Perjanjian, $this>
     */
    public function perjanjian(): BelongsTo
    {
        return $this->belongsTo(Perjanjian::class);
    }
}
