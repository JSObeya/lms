<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvetOccupationalStandard extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_category_id',
        'code',
        'title',
        'source_authority',
        'version',
        'is_official',
        'performance_criteria',
        'created_by',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_official' => 'boolean',
            'is_active' => 'boolean',
            'performance_criteria' => 'array',
        ];
    }

    public function tradeCategory(): BelongsTo
    {
        return $this->belongsTo(TradeCategory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CompetencyLesson::class, 'occupational_standard_id');
    }
}
