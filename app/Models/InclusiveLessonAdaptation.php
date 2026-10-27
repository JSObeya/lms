<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InclusiveLessonAdaptation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'competency_lesson_id',
        'user_id',
        'structured_plan',
        'differentiated_activities',
        'udl_engagement',
        'udl_representation',
        'udl_action_expression',
        'learner_accommodations',
        'safety_standards_maintained',
        'gender_responsive_strategies',
        'workshop_rotation_schedule',
        'workshop_safety_arrangements',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'safety_standards_maintained' => 'boolean',
            'structured_plan' => 'array',
            'differentiated_activities' => 'array',
            'udl_engagement' => 'array',
            'udl_representation' => 'array',
            'udl_action_expression' => 'array',
            'learner_accommodations' => 'array',
            'gender_responsive_strategies' => 'array',
            'workshop_rotation_schedule' => 'array',
            'workshop_safety_arrangements' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function competencyLesson(): BelongsTo
    {
        return $this->belongsTo(CompetencyLesson::class, 'competency_lesson_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PedagogyReview::class, 'inclusive_adaptation_id');
    }

    /**
     * Day 10 Verification: 3-tier differentiated activities (Beginner, Intermediate, Advanced).
     */
    public function hasDifferentiatedActivities(): bool
    {
        $activities = $this->differentiated_activities;
        if (! is_array($activities)) {
            return false;
        }

        $hasBeginner = ! empty($activities['beginner']) && count($activities['beginner']) > 0;
        $hasIntermediate = ! empty($activities['intermediate']) && count($activities['intermediate']) > 0;
        $hasAdvanced = ! empty($activities['advanced']) && count($activities['advanced']) > 0;

        return $hasBeginner && $hasIntermediate && $hasAdvanced;
    }

    /**
     * Day 10 Verification: Universal Design for Learning (UDL) principles applied.
     */
    public function hasUdlAdaptations(): bool
    {
        $engagement = $this->udl_engagement;
        $representation = $this->udl_representation;
        $action = $this->udl_action_expression;

        $hasEngagement = is_array($engagement) && count($engagement) > 0;
        $hasRep = is_array($representation) && count($representation) > 0;
        $hasAction = is_array($action) && count($action) > 0;

        return $hasEngagement && $hasRep && $hasAction;
    }

    /**
     * Day 10 Verification: Essential occupational safety standards are non-negotiable.
     */
    public function isSafetyVerified(): bool
    {
        return $this->safety_standards_maintained === true;
    }

    /**
     * Check if Day 10 inclusive lesson adaptation is complete.
     */
    public function isDay10Compliant(): bool
    {
        return $this->hasDifferentiatedActivities() &&
            $this->hasUdlAdaptations() &&
            $this->isSafetyVerified();
    }
}
