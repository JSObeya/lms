<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RubricAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_submission_id',
        'rubric_id',
        'assessed_by',
        'total_calculated_score',
        'general_feedback',
        'is_released',
    ];

    protected function casts(): array
    {
        return [
            'total_calculated_score' => 'decimal:2',
            'is_released' => 'boolean',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RubricAssessmentItem::class);
    }
}
