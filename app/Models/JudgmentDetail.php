<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JudgmentDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sessionId',
        'criteriaId',
        'projectId',
        'criteria_point',
        'criteria_percentage',
        'criteria_parent_id',
        'criteria_type',
        'criteria_name'
    ];

    protected $casts = [
        'criteria_point' => 'decimal:2',
        'criteria_percentage' => 'integer',
        'criteria_parent_id' => 'integer',
        'criteria_type' => 'integer'
    ];

    // Relationships
    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class, 'criteriaId');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'projectId');
    }

    public function parentCriteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class, 'criteria_parent_id');
    }

    public function criteriaType(): BelongsTo
    {
        return $this->belongsTo(CriteriaType::class, 'criteria_type');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('projectId', $projectId);
    }

    public function scopeByCriteria($query, $criteriaId)
    {
        return $query->where('criteriaId', $criteriaId);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('sessionId', $sessionId);
    }
}
