<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectLocation extends Model
{
    protected $fillable = [
        'project_id',
        'location_id',
        'total_score',
        'ranking',
        'notes'
    ];

    protected $casts = [
        'total_score' => 'decimal:2'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function calculateTotalScore()
    {
        $totalScore = 0;
        $totalWeight = 0;

        foreach ($this->scores as $score) {
            $criteria = $score->criteria;
            $weightedScore = ($score->score / $criteria->max_score) * $criteria->weight;
            $totalScore += $weightedScore;
            $totalWeight += $criteria->weight;
        }

        if ($totalWeight > 0) {
            $this->total_score = ($totalScore / $totalWeight) * 100;
            $this->save();
        }

        return $this->total_score;
    }
}
