<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetencyLesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'user_id',
        'trade_category_id',
        'occupational_standard_id',
        'lesson_id',
        'title',
        'occupational_task',
        'duration_minutes',
        'target_level',
        'learning_outcomes',
        'demonstration_plan',
        'guided_practice',
        'independent_practice',
        'project_based_tasks',
        'fault_diagnosis_scenarios',
        'work_based_learning',
        'assessment_criteria',
        'status',
        'version',
        'is_template',
        'template_trade',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'version' => 'integer',
            'is_template' => 'boolean',
            'learning_outcomes' => 'array',
            'demonstration_plan' => 'array',
            'guided_practice' => 'array',
            'independent_practice' => 'array',
            'project_based_tasks' => 'array',
            'fault_diagnosis_scenarios' => 'array',
            'work_based_learning' => 'array',
            'assessment_criteria' => 'array',
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

    public function tradeCategory(): BelongsTo
    {
        return $this->belongsTo(TradeCategory::class);
    }

    public function occupationalStandard(): BelongsTo
    {
        return $this->belongsTo(TvetOccupationalStandard::class, 'occupational_standard_id');
    }

    public function syllabusLesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function inclusiveAdaptation(): HasOne
    {
        return $this->hasOne(InclusiveLessonAdaptation::class, 'competency_lesson_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PedagogyReview::class, 'competency_lesson_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(CompetencyLessonRevision::class, 'competency_lesson_id');
    }

    /**
     * Day 9 Practical Requirement Verification:
     * Has instructor demonstration plan.
     */
    public function hasDemonstrationPlan(): bool
    {
        $plan = $this->demonstration_plan;

        return is_array($plan) && count($plan) > 0 && ! empty($plan[0]['instructor_action'] ?? null);
    }

    /**
     * Day 9 Practical Requirement Verification:
     * Has learner guided and/or independent practice exercises.
     */
    public function hasLearnerExercises(): bool
    {
        $guided = $this->guided_practice;
        $independent = $this->independent_practice;

        $hasGuided = is_array($guided) && count($guided) > 0 && ! empty($guided[0]['activity'] ?? null);
        $hasIndep = is_array($independent) && count($independent) > 0 && ! empty($independent[0]['activity'] ?? null);

        return $hasGuided || $hasIndep;
    }

    /**
     * Day 9 Practical Requirement Verification:
     * Has measurable assessment criteria and evidence requirements.
     */
    public function hasMeasurableAssessmentCriteria(): bool
    {
        $criteria = $this->assessment_criteria;

        return is_array($criteria) && count($criteria) > 0 && ! empty($criteria[0]['criterion'] ?? null);
    }

    /**
     * Check if all Day 9 practical lesson builder requirements are met.
     */
    public function isDay9Compliant(): bool
    {
        return ! empty($this->occupational_task) &&
            $this->hasDemonstrationPlan() &&
            $this->hasLearnerExercises() &&
            $this->hasMeasurableAssessmentCriteria();
    }
}
