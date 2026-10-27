<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyLessonRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'competency_lesson_id',
        'user_id',
        'version',
        'snapshot_data',
        'change_summary',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'snapshot_data' => 'array',
        ];
    }

    public function competencyLesson(): BelongsTo
    {
        return $this->belongsTo(CompetencyLesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
