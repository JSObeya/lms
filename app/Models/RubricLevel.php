<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RubricLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'rubric_criterion_id',
        'title',
        'descriptor',
        'points',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'decimal:2',
            'order_index' => 'integer',
        ];
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(RubricCriterion::class, 'rubric_criterion_id');
    }
}
