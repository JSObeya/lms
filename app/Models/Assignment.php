<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'course_section_id',
        'lesson_id',
        'rubric_id',
        'title',
        'instructions',
        'max_score',
        'allow_submissions_from',
        'due_date',
        'cut_off_date',
        'timezone',
        'submission_type',
        'accepted_file_types',
        'max_files',
        'max_file_size_mb',
        'late_submission_policy',
        'penalty_percentage',
        'attempt_limit',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'allow_submissions_from' => 'datetime',
            'due_date' => 'datetime',
            'cut_off_date' => 'datetime',
            'max_files' => 'integer',
            'max_file_size_mb' => 'integer',
            'penalty_percentage' => 'integer',
            'attempt_limit' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function isPastDue(): bool
    {
        if (! $this->due_date) {
            return false;
        }

        return Carbon::now($this->timezone)->isAfter($this->due_date);
    }

    public function isCutOffReached(): bool
    {
        if (! $this->cut_off_date) {
            return false;
        }

        return Carbon::now($this->timezone)->isAfter($this->cut_off_date);
    }
}
