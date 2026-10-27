<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyEvidence extends Model
{
    use HasFactory;

    protected $table = 'competency_evidence';

    protected $fillable = [
        'practical_assessment_id',
        'title',
        'file_path',
        'file_type',
        'comments',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(PracticalAssessment::class, 'practical_assessment_id');
    }
}
