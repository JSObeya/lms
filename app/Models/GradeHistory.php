<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_id',
        'previous_score',
        'new_score',
        'changed_by',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'previous_score' => 'decimal:2',
            'new_score' => 'decimal:2',
        ];
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
