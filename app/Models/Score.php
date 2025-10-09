<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    protected $fillable = [
        'project_location_id',
        'criteria_id',
        'score',
        'notes'
    ];

    protected $casts = [
        'score' => 'decimal:2'
    ];

    public function projectLocation(): BelongsTo
    {
        return $this->belongsTo(ProjectLocation::class);
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class);
    }
}
