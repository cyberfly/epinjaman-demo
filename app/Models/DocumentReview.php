<?php

namespace App\Models;

use Database\Factories\DocumentReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $permohonan_id
 * @property int $checklist_item_id
 * @property int|null $reviewed_by
 * @property bool $is_complete
 * @property bool $is_orderly
 * @property string|null $catatan
 */
class DocumentReview extends Model
{
    /** @use HasFactory<DocumentReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'checklist_item_id',
        'reviewed_by',
        'is_complete',
        'is_orderly',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_complete' => 'boolean',
            'is_orderly' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ChecklistItem, $this>
     */
    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class);
    }
}
