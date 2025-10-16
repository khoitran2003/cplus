<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Criteria;
use App\Models\Location;
use App\Models\JudgmentDetail;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function statistics()
    {
        $stats = [
            'total_clients' => Client::count(),
            'total_projects' => Project::active()->count(),
            'total_locations' => Location::where('is_active', true)->count(),
            'total_criteria' => Criteria::active()->count(),
            'active_projects' => Project::active()->count(),
            'completed_evaluations' => JudgmentDetail::whereNotNull('criteria_point')->count(),
            'pending_evaluations' => JudgmentDetail::whereNull('criteria_point')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get recent projects
     */
    public function recentProjects()
    {
        $projects = Project::with(['client', 'projectLocations.location'])
                          ->active()
                          ->latest()
                          ->take(10)
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get project scoring status
     */
    public function scoringStatus()
    {
        $status = [
            'completed' => JudgmentDetail::whereNotNull('criteria_point')->count(),
            'in_progress' => JudgmentDetail::whereNull('criteria_point')->whereHas('criteria')->count(),
            'not_started' => Project::active()->whereDoesntHave('judgmentDetails')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }

    /**
     * Get project comparison data
     */
    public function projectComparison()
    {
        $projects = Project::with(['judgmentDetails'])
                          ->active()
                          ->get()
                          ->map(function ($project) {
                              $avgScore = $project->judgmentDetails->whereNotNull('criteria_point')->avg('criteria_point');
                              return [
                                  'id' => $project->id,
                                  'name' => $project->project_name,
                                  'average_score' => $avgScore ? round($avgScore, 2) : 0,
                                  'total_evaluations' => $project->judgmentDetails->count(),
                                  'completed_evaluations' => $project->judgmentDetails->whereNotNull('criteria_point')->count()
                              ];
                          });

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get criteria usage statistics
     */
    public function criteriaUsage()
    {
        $criteria = Criteria::withCount('judgmentDetails')
                           ->active()
                           ->orderBy('judgment_details_count', 'desc')
                           ->take(10)
                           ->get()
                           ->map(function ($criterion) {
                               return [
                                   'id' => $criterion->id,
                                   'name' => $criterion->criteria_name,
                                   'type' => $criterion->criteriaType->type_name ?? 'N/A',
                                   'usage_count' => $criterion->judgment_details_count,
                                   'full_path' => $criterion->getFullPath()
                               ];
                           });

        return response()->json([
            'success' => true,
            'data' => $criteria
        ]);
    }

    /**
     * Get client statistics
     */
    public function clientStatistics()
    {
        $clients = Client::withCount('projects')
                        ->orderBy('projects_count', 'desc')
                        ->take(10)
                        ->get()
                        ->map(function ($client) {
                            return [
                                'id' => $client->id,
                                'name' => $client->name,
                                'projects_count' => $client->projects_count,
                                'email' => $client->email,
                                'company' => $client->company
                            ];
                        });

        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }

    /**
     * Get monthly statistics
     */
    public function monthlyStatistics(Request $request)
    {
        $months = $request->get('months', 12);
        
        $monthlyData = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->startOfMonth();
            $monthEnd = $date->endOfMonth();
            
            $monthlyData[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('M Y'),
                'projects_created' => Project::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'evaluations_completed' => JudgmentDetail::whereBetween('created_at', [$monthStart, $monthEnd])
                                                       ->whereNotNull('criteria_point')
                                                       ->count()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $monthlyData
        ]);
    }

    /**
     * Get top performing locations
     */
    public function topLocations()
    {
        $locations = Location::with(['projectLocations'])
                            ->where('is_active', true)
                            ->get()
                            ->map(function ($location) {
                                $avgScore = $location->projectLocations->whereNotNull('total_score')->avg('total_score');
                                return [
                                    'id' => $location->id,
                                    'name' => $location->name,
                                    'city' => $location->city,
                                    'province' => $location->province,
                                    'average_score' => $avgScore ? round($avgScore, 2) : 0,
                                    'projects_count' => $location->projectLocations->count()
                                ];
                            })
                            ->sortByDesc('average_score')
                            ->take(10)
                            ->values();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Get scoring trends
     */
    public function scoringTrends(Request $request)
    {
        $criteriaId = $request->get('criteria_id');
        $projectId = $request->get('project_id');
        
        $query = JudgmentDetail::with(['criteria', 'project'])
                              ->whereNotNull('criteria_point')
                              ->active();

        if ($criteriaId) {
            $query->where('criteriaId', $criteriaId);
        }

        if ($projectId) {
            $query->where('projectId', $projectId);
        }

        $trends = $query->orderBy('created_at')
                       ->get()
                       ->groupBy(function ($item) {
                           return $item->created_at->format('Y-m');
                       })
                       ->map(function ($group, $month) {
                           return [
                               'month' => $month,
                               'average_score' => round($group->avg('criteria_point'), 2),
                               'total_evaluations' => $group->count(),
                               'criteria' => $group->pluck('criteria.criteria_name')->unique()->values()
                           ];
                       })
                       ->values();

        return response()->json([
            'success' => true,
            'data' => $trends
        ]);
    }
}
