<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'started_at',
        'completed_at',
        'status',
        'score',
        'max_score',
        'total_questions',
        'correct_answers_count',
        'incorrect_answers_count',
        'unanswered_count',
        'percentage',
        'passed',
        'grade',
        'answers',
        'category_scores',
        'evaluator_feedback',
        'evaluated_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'integer',
        'max_score' => 'integer',
        'total_questions' => 'integer',
        'correct_answers_count' => 'integer',
        'incorrect_answers_count' => 'integer',
        'unanswered_count' => 'integer',
        'percentage' => 'float',
        'passed' => 'boolean',
        'answers' => 'array',
        'category_scores' => 'array',
    ];

    protected $appends = [
        'formatted_duration',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->started_at || ! $this->completed_at) {
                    return 'N/A';
                }

                $diffInSeconds = $this->started_at->diffInSeconds($this->completed_at);
                $minutes = floor($diffInSeconds / 60);
                $seconds = $diffInSeconds % 60;

                if ($minutes > 0) {
                    return "{$minutes}m {$seconds}s";
                }

                return "{$seconds}s";
            }
        );
    }
}
