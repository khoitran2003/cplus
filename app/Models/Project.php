<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'description',
        'industry',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projectLocations(): HasMany
    {
        return $this->hasMany(ProjectLocation::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'project_locations')
                    ->withPivot('total_score', 'ranking', 'notes')
                    ->withTimestamps();
    }
}
