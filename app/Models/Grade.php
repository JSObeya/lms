<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'assignment_id',
        'user_id',
        'max_score',
        'obtained_score',
        'percentage',
        'is_override',
        'override_reason',
        'is_released',
        'feedback',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'obtained_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'is_override' => 'boolean',
            'is_released' => 'boolean',
            'graded_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function history(): HasMany
    {
        return $this->hasMany(GradeHistory::class);
    }
}
