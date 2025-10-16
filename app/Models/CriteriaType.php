<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CriteriaType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type_name'
    ];

    public function criteria(): HasMany
    {
        return $this->hasMany(Criteria::class, 'criteriaTypeId');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
