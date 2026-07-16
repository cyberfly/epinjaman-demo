<?php

namespace App\Models;

use Database\Factories\PermohonanDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property int|null $checklist_item_id
 * @property int|null $uploaded_by
 * @property string $path
 * @property string $original_name
 */
class PermohonanDocument extends Model
{
    /** @use HasFactory<PermohonanDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'checklist_item_id',
        'uploaded_by',
        'path',
        'original_name',
    ];

    /**
     * @return BelongsTo<Permohonan, $this>
     */
    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(Permohonan::class);
    }

    /**
     * @return BelongsTo<ChecklistItem, $this>
     */
    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class);
    }
}
