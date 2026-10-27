<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvetCompetency extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'trade_category_id',
        'occupational_task',
        'required_knowledge',
        'required_skills',
        'performance_conditions',
        'required_tools',
        'safety_precautions',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function tradeCategory(): BelongsTo
    {
        return $this->belongsTo(TradeCategory::class);
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(TvetPerformanceCriterion::class)->orderBy('order_index');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(PracticalAssessment::class);
    }
}
