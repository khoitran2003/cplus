<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'city',
        'province',
        'country',
        'latitude',
        'longitude',
        'description',
        'is_active'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    public function projectLocations(): HasMany
    {
        return $this->hasMany(ProjectLocation::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_locations')
                    ->withPivot('total_score', 'ranking', 'notes')
                    ->withTimestamps();
    }
}
