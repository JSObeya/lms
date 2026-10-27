<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedagogyReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'competency_lesson_id',
        'inclusive_adaptation_id',
        'teacher_id',
        'facilitator_id',
        'module_type',
        'status',
        'rubric_scores',
        'total_score',
        'max_score',
        'written_feedback',
        'revision_notes',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'rubric_scores' => 'array',
            'total_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function competencyLesson(): BelongsTo
    {
        return $this->belongsTo(CompetencyLesson::class);
    }

    public function inclusiveAdaptation(): BelongsTo
    {
        return $this->belongsTo(InclusiveLessonAdaptation::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'facilitator_id');
    }
}
