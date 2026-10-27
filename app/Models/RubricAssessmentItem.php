<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RubricAssessmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rubric_assessment_id',
        'rubric_criterion_id',
        'rubric_level_id',
        'awarded_points',
        'assessor_comment',
    ];

    protected function casts(): array
    {
        return [
            'awarded_points' => 'decimal:2',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(RubricAssessment::class, 'rubric_assessment_id');
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(RubricCriterion::class, 'rubric_criterion_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(RubricLevel::class, 'rubric_level_id');
    }
}
