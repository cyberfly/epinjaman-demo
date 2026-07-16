<?php

namespace App\Models;

use App\Enums\PemohonStatus;
use Database\Factories\PemohonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A Pemohon organisation (government agency / GLC) applying for a loan.
 * Its account is provisioned by SID, may have several users, and may hold
 * several Permohonan over time.
 *
 * @property int $id
 * @property string $nama
 * @property PemohonStatus $status
 */
class Pemohon extends Model
{
    /** @use HasFactory<PemohonFactory> */
    use HasFactory;

    protected $fillable = ['nama', 'status'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PemohonStatus::class,
        ];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * A Pemohon organisation may hold several Permohonan over time.
     *
     * @return HasMany<Permohonan, $this>
     */
    public function permohonans(): HasMany
    {
        return $this->hasMany(Permohonan::class);
    }

    public function isPeminjam(): bool
    {
        return $this->status === PemohonStatus::Peminjam;
    }
}
