<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Day8PracticeProgress extends Model
{
    use HasFactory;

    protected $table = 'day8_practice_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'step_1_course_created',
        'step_2_topic_labeled',
        'step_3_objective_saved',
        'step_4_resource_attached',
        'step_5_assignment_created',
        'step_6_deadline_configured',
        'step_7_rubric_activated',
        'step_8_preview_viewed',
        'step_9_sample_graded',
        'step_10_report_exported',
        'is_verified_by_facilitator',
        'verified_by',
        'facilitator_feedback',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'step_1_course_created' => 'boolean',
            'step_2_topic_labeled' => 'boolean',
            'step_3_objective_saved' => 'boolean',
            'step_4_resource_attached' => 'boolean',
            'step_5_assignment_created' => 'boolean',
            'step_6_deadline_configured' => 'boolean',
            'step_7_rubric_activated' => 'boolean',
            'step_8_preview_viewed' => 'boolean',
            'step_9_sample_graded' => 'boolean',
            'step_10_report_exported' => 'boolean',
            'is_verified_by_facilitator' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function completedStepsCount(): int
    {
        $steps = [
            $this->step_1_course_created,
            $this->step_2_topic_labeled,
            $this->step_3_objective_saved,
            $this->step_4_resource_attached,
            $this->step_5_assignment_created,
            $this->step_6_deadline_configured,
            $this->step_7_rubric_activated,
            $this->step_8_preview_viewed,
            $this->step_9_sample_graded,
            $this->step_10_report_exported,
        ];

        return count(array_filter($steps));
    }

    public function isFullyCompleted(): bool
    {
        return $this->completedStepsCount() === 10;
    }

    public function progressPercentage(): int
    {
        return (int) round(($this->completedStepsCount() / 10) * 100);
    }
}
