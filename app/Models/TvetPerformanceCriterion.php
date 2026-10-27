<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TvetPerformanceCriterion extends Model
{
    use HasFactory;

    protected $fillable = [
        'tvet_competency_id',
        'criterion_text',
        'is_critical_safety',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'is_critical_safety' => 'boolean',
            'order_index' => 'integer',
        ];
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(TvetCompetency::class, 'tvet_competency_id');
    }
}
