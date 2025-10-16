<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_name',
        'photo',
        'clientId',
        'userId'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function projectLocations(): HasMany
    {
        return $this->hasMany(ProjectLocation::class);
    }

    public function judgmentDetails(): HasMany
    {
        return $this->hasMany(JudgmentDetail::class, 'projectId');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'project_locations')
                    ->withPivot('total_score', 'ranking', 'notes')
                    ->withTimestamps();
    }

    // Accessor for backward compatibility
    public function getNameAttribute()
    {
        return $this->project_name;
    }

    public function getDescriptionAttribute()
    {
        return "Project: " . $this->project_name;
    }

    public function getIndustryAttribute()
    {
        return $this->client->industry ?? 'General';
    }

    public function getStatusAttribute()
    {
        return 'active'; // Default status
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('clientId', $clientId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('userId', $userId);
    }

    // Helper methods
    public function getTotalScore()
    {
        return $this->judgmentDetails()->sum('criteria_point');
    }

    public function getAverageScore()
    {
        $totalPoints = $this->judgmentDetails()->sum('criteria_point');
        $totalCriteria = $this->judgmentDetails()->count();
        
        return $totalCriteria > 0 ? $totalPoints / $totalCriteria : 0;
    }
}
