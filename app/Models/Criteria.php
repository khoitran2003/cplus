<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criteria extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'weight',
        'scoring_type',
        'max_score',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
