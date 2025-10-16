<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Location;
use App\Models\Criteria;
use App\Models\JudgmentDetail;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    /**
     * Get all projects
     */
    public function index(Request $request)
    {
        $query = Project::with(['client', 'user'])
                       ->active();

        // Filter by client
        if ($request->has('client_id')) {
            $query->byClient($request->client_id);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->byUser($request->user_id);
        }

        $projects = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get single project with details
     */
    public function show(Project $project)
    {
        $project->load([
            'client',
            'user',
            'projectLocations.location',
            'judgmentDetails.criteria.criteriaType'
        ]);

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    /**
     * Get project scoring interface data
     */
    public function scoring(Project $project)
    {
        $project->load(['projectLocations.location', 'judgmentDetails.criteria']);
        
        // Get active criteria
        $criteria = Criteria::with(['criteriaType', 'parent'])
                           ->active()
                           ->orderBy('parentId', 'asc')
                           ->orderBy('criteria_name', 'asc')
                           ->get();

        // Calculate total scores if not already calculated
        foreach ($project->projectLocations as $projectLocation) {
            if ($projectLocation->total_score === null) {
                $projectLocation->calculateTotalScore();
            }
        }

        // Sort by total score descending
        $project->projectLocations = $project->projectLocations->sortByDesc('total_score')->values();

        return response()->json([
            'success' => true,
            'data' => [
                'project' => $project,
                'criteria' => $criteria
            ]
        ]);
    }

    /**
     * Save project scoring data
     */
    public function saveScoring(Request $request, Project $project)
    {
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*.criteriaId' => 'required|exists:criteria,id',
            'scores.*.criteria_point' => 'required|numeric|min:0',
            'scores.*.criteria_percentage' => 'required|integer|min:0|max:100',
            'scores.*.criteria_parent_id' => 'nullable|exists:criteria,id',
            'scores.*.criteria_type' => 'nullable|integer',
            'scores.*.criteria_name' => 'nullable|string'
        ]);

        \DB::transaction(function () use ($validated, $project) {
            foreach ($validated['scores'] as $scoreData) {
                JudgmentDetail::updateOrCreate(
                    [
                        'criteriaId' => $scoreData['criteriaId'],
                        'projectId' => $project->id
                    ],
                    [
                        'criteria_point' => $scoreData['criteria_point'],
                        'criteria_percentage' => $scoreData['criteria_percentage'],
                        'criteria_parent_id' => $scoreData['criteria_parent_id'] ?? null,
                        'criteria_type' => $scoreData['criteria_type'] ?? null,
                        'criteria_name' => $scoreData['criteria_name'] ?? null
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Scores saved successfully'
        ]);
    }

    /**
     * Export project results
     */
    public function export(Project $project, $format = 'pdf')
    {
        $project->load([
            'client',
            'projectLocations.location',
            'judgmentDetails.criteria.criteriaType'
        ]);

        // Calculate rankings
        $project->projectLocations = $project->projectLocations->sortByDesc('total_score');
        $rank = 1;
        foreach ($project->projectLocations as $projectLocation) {
            $projectLocation->ranking = $rank++;
        }

        if ($format === 'excel') {
            return $this->exportToExcel($project);
        } else {
            return $this->exportToPdf($project);
        }
    }

    private function exportToPdf(Project $project)
    {
        // TODO: Implement PDF export using a package like dompdf or tcpdf
        return response()->json([
            'success' => false,
            'message' => 'PDF export not implemented yet'
        ]);
    }

    private function exportToExcel(Project $project)
    {
        // TODO: Implement Excel export using a package like maatwebsite/excel
        return response()->json([
            'success' => false,
            'message' => 'Excel export not implemented yet'
        ]);
    }

    /**
     * Get project statistics
     */
    public function statistics(Project $project)
    {
        $stats = [
            'total_locations' => $project->projectLocations->count(),
            'total_criteria' => $project->judgmentDetails->count(),
            'average_score' => $project->getAverageScore(),
            'total_score' => $project->getTotalScore(),
            'completed_evaluations' => $project->judgmentDetails()->whereNotNull('criteria_point')->count(),
            'pending_evaluations' => $project->judgmentDetails()->whereNull('criteria_point')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
