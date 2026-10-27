<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_portfolio_id',
        'category',
        'title',
        'description',
        'file_path',
        'url',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(TeacherPortfolio::class, 'teacher_portfolio_id');
    }
}
