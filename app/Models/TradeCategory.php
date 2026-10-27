<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TradeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_tvet_trade',
        'order_index',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_tvet_trade' => 'boolean',
            'is_active' => 'boolean',
            'order_index' => 'integer',
        ];
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'trade_category_id');
    }
}
