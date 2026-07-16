<?php

namespace App\Models;

use Database\Factories\KementerianPengawalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A controlling ministry (Kementerian Pengawal) that supervises a Pemohon for
 * KWAPBB-sourced applications and signs off at stage 2.
 *
 * @property int $id
 * @property string $nama
 */
class KementerianPengawal extends Model
{
    /** @use HasFactory<KementerianPengawalFactory> */
    use HasFactory;

    protected $fillable = ['nama'];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
