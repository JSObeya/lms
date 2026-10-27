<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'code',
        'trade_category_id',
        'user_id',
        'summary',
        'description',
        'thumbnail',
        'objectives_overview',
        'duration',
        'difficulty_level',
        'start_date',
        'end_date',
        'enrollment_type',
        'visibility',
        'status',
        'is_practice',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_practice' => 'boolean',
            'settings' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TradeCategory::class, 'trade_category_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order_index');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order_index');
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(LearningObjective::class)->orderBy('order_index');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class)->orderBy('order_index');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(Rubric::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
            ->wherePivot('role', 'student')
            ->withPivot('status', 'progress_percentage', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    public function coInstructors(): HasMany
    {
        return $this->hasMany(CourseInstructor::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function competencies(): HasMany
    {
        return $this->hasMany(TvetCompetency::class);
    }

    public function competencyLessons(): HasMany
    {
        return $this->hasMany(CompetencyLesson::class);
    }

    /**
     * Query Scopes.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopePractice(Builder $query): Builder
    {
        return $query->where('is_practice', true);
    }

    public function scopeInstitutional(Builder $query): Builder
    {
        return $query->where('is_practice', false);
    }

    public function scopeForTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('user_id', $teacherId);
    }
}
