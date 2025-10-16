<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Criteria extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'criteria_name',
        'criteriaTypeId',
        'parentId',
        'clientId',
        'criteriaPercent'
    ];

    protected $casts = [
        'criteriaPercent' => 'decimal:2'
    ];

    // Relationships
    public function criteriaType(): BelongsTo
    {
        return $this->belongsTo(CriteriaType::class, 'criteriaTypeId');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Criteria::class, 'parentId');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Criteria::class, 'parentId');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function judgmentDetails(): HasMany
    {
        return $this->hasMany(JudgmentDetail::class, 'criteriaId');
    }

    public function industries(): BelongsToMany
    {
        return $this->belongsToMany(Industry::class, 'criteria_industry', 'criteriaId', 'industryId');
    }

    // Accessor for backward compatibility
    public function getNameAttribute()
    {
        return $this->criteria_name;
    }

    public function getDescriptionAttribute()
    {
        return $this->criteriaType->type_name ?? '';
    }

    public function getTypeAttribute()
    {
        return $this->criteriaType->type_name ?? 'quantitative';
    }

    public function getWeightAttribute()
    {
        return $this->criteriaPercent ?? 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('criteriaTypeId', $typeId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('clientId', $clientId);
    }

    public function scopeRootCriteria($query)
    {
        return $query->whereNull('parentId');
    }

    public function scopeChildCriteria($query, $parentId)
    {
        return $query->where('parentId', $parentId);
    }

    // Helper methods
    public function isRoot()
    {
        return is_null($this->parentId);
    }

    public function isChild()
    {
        return !is_null($this->parentId);
    }

    public function getFullPath()
    {
        $path = [$this->criteria_name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->criteria_name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }
}
