<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Industry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'industry_name'
    ];

    public function criteria(): BelongsToMany
    {
        return $this->belongsToMany(Criteria::class, 'criteria_industry', 'industryId', 'criteriaId');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
