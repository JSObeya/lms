<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedagogyTrainingProgress extends Model
{
    use HasFactory;

    protected $table = 'pedagogy_training_progress';

    protected $fillable = [
        'user_id',
        'competency_lesson_id',
        'inclusive_adaptation_id',
        'day9_demonstration_ready',
        'day9_exercise_ready',
        'day9_criteria_ready',
        'day9_completed',
        'day9_completed_at',
        'day9_verified_by',
        'day10_differentiated_ready',
        'day10_udl_ready',
        'day10_safety_verified',
        'day10_completed',
        'day10_completed_at',
        'day10_verified_by',
        'facilitator_feedback',
    ];

    protected function casts(): array
    {
        return [
            'day9_demonstration_ready' => 'boolean',
            'day9_exercise_ready' => 'boolean',
            'day9_criteria_ready' => 'boolean',
            'day9_completed' => 'boolean',
            'day9_completed_at' => 'datetime',
            'day10_differentiated_ready' => 'boolean',
            'day10_udl_ready' => 'boolean',
            'day10_safety_verified' => 'boolean',
            'day10_completed' => 'boolean',
            'day10_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function competencyLesson(): BelongsTo
    {
        return $this->belongsTo(CompetencyLesson::class);
    }

    public function inclusiveAdaptation(): BelongsTo
    {
        return $this->belongsTo(InclusiveLessonAdaptation::class);
    }

    public function day9Verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'day9_verified_by');
    }

    public function day10Verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'day10_verified_by');
    }

    public function recalculateForLesson(?CompetencyLesson $lesson = null): void
    {
        $lesson = $lesson ?: $this->competencyLesson;
        if (! $lesson) {
            return;
        }

        $this->day9_demonstration_ready = $lesson->hasDemonstrationPlan();
        $this->day9_exercise_ready = $lesson->hasLearnerExercises();
        $this->day9_criteria_ready = $lesson->hasMeasurableAssessmentCriteria();
        $this->day9_completed = $this->day9_demonstration_ready && $this->day9_exercise_ready && $this->day9_criteria_ready;

        if ($this->day9_completed && ! $this->day9_completed_at) {
            $this->day9_completed_at = now();
        }

        $adaptation = $lesson->inclusiveAdaptation;
        if ($adaptation) {
            $this->inclusive_adaptation_id = $adaptation->id;
            $this->day10_differentiated_ready = $adaptation->hasDifferentiatedActivities();
            $this->day10_udl_ready = $adaptation->hasUdlAdaptations();
            $this->day10_safety_verified = $adaptation->isSafetyVerified();
            $this->day10_completed = $this->day10_differentiated_ready && $this->day10_udl_ready && $this->day10_safety_verified;

            if ($this->day10_completed && ! $this->day10_completed_at) {
                $this->day10_completed_at = now();
            }
        }

        $this->save();
    }
}
