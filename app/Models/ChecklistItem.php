<?php

namespace App\Models;

use Database\Factories\ChecklistItemFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Lampiran 6 — an admin-managed document checklist item.
 *
 * @property int $id
 * @property string $label
 * @property string|null $description
 * @property int $position
 * @property bool $is_active
 */
class ChecklistItem extends Model
{
    /** @use HasFactory<ChecklistItemFactory> */
    use HasFactory;

    protected $fillable = ['label', 'description', 'position', 'is_active'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * The active checklist, ordered for display and review (internal API used
     * by later tickets: form completeness, PSID review, CP documents).
     *
     * @return Collection<int, static>
     */
    public static function active(): Collection
    {
        return static::query()->where('is_active', true)->orderBy('position')->orderBy('id')->get();
    }
}
