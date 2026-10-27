<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'bio',
        'completion_percentage',
        'is_shared',
        'shared_with_facilitator_id',
    ];

    protected function casts(): array
    {
        return [
            'completion_percentage' => 'integer',
            'is_shared' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_facilitator_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }
}
