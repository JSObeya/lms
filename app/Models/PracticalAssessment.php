<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticalAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tvet_competency_id',
        'user_id',
        'assessor_id',
        'outcome',
        'safety_passed',
        'assessor_comments',
        'reassessment_date',
    ];

    protected function casts(): array
    {
        return [
            'safety_passed' => 'boolean',
            'reassessment_date' => 'date',
        ];
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(TvetCompetency::class, 'tvet_competency_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(CompetencyEvidence::class);
    }
}
