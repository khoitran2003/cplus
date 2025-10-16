<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JudgmentDetail;
use App\Models\Project;
use App\Models\Criteria;
use Illuminate\Http\Request;

class JudgmentApiController extends Controller
{
    /**
     * Get all judgment details
     */
    public function index(Request $request)
    {
        $query = JudgmentDetail::with(['criteria.criteriaType', 'project.client'])
                              ->active();

        // Filter by project
        if ($request->has('project_id')) {
            $query->byProject($request->project_id);
        }

        // Filter by criteria
        if ($request->has('criteria_id')) {
            $query->byCriteria($request->criteria_id);
        }

        // Filter by session
        if ($request->has('session_id')) {
            $query->bySession($request->session_id);
        }

        $judgmentDetails = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $judgmentDetails
        ]);
    }

    /**
     * Store judgment detail
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sessionId' => 'nullable|integer',
            'criteriaId' => 'required|exists:criteria,id',
            'projectId' => 'nullable|exists:projects,id',
            'criteria_point' => 'nullable|numeric|min:0',
            'criteria_percentage' => 'required|integer|min:0|max:100',
            'criteria_parent_id' => 'nullable|exists:criteria,id',
            'criteria_type' => 'nullable|integer',
            'criteria_name' => 'nullable|string|max:255'
        ]);

        $judgmentDetail = JudgmentDetail::create($validated);
        $judgmentDetail->load(['criteria.criteriaType', 'project']);

        return response()->json([
            'success' => true,
            'message' => 'Judgment detail created successfully',
            'data' => $judgmentDetail
        ], 201);
    }

    /**
     * Update judgment detail
     */
    public function update(Request $request, JudgmentDetail $judgmentDetail)
    {
        $validated = $request->validate([
            'sessionId' => 'nullable|integer',
            'criteriaId' => 'required|exists:criteria,id',
            'projectId' => 'nullable|exists:projects,id',
            'criteria_point' => 'nullable|numeric|min:0',
            'criteria_percentage' => 'required|integer|min:0|max:100',
            'criteria_parent_id' => 'nullable|exists:criteria,id',
            'criteria_type' => 'nullable|integer',
            'criteria_name' => 'nullable|string|max:255'
        ]);

        $judgmentDetail->update($validated);
        $judgmentDetail->load(['criteria.criteriaType', 'project']);

        return response()->json([
            'success' => true,
            'message' => 'Judgment detail updated successfully',
            'data' => $judgmentDetail
        ]);
    }

    /**
     * Delete judgment detail
     */
    public function destroy(JudgmentDetail $judgmentDetail)
    {
        $judgmentDetail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Judgment detail deleted successfully'
        ]);
    }

    /**
     * Get judgment details by project
     */
    public function byProject(Project $project)
    {
        $judgmentDetails = JudgmentDetail::with(['criteria.criteriaType'])
                                        ->byProject($project->id)
                                        ->active()
                                        ->get();

        // Group by criteria type for better organization
        $grouped = $judgmentDetails->groupBy('criteria.criteriaType.type_name');

        return response()->json([
            'success' => true,
            'data' => [
                'project' => $project->load('client'),
                'judgment_details' => $judgmentDetails,
                'grouped_by_type' => $grouped
            ]
        ]);
    }

    /**
     * Get judgment details by criteria
     */
    public function byCriteria(Criteria $criteria)
    {
        $judgmentDetails = JudgmentDetail::with(['project.client'])
                                        ->byCriteria($criteria->id)
                                        ->active()
                                        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'criteria' => $criteria->load('criteriaType'),
                'judgment_details' => $judgmentDetails
            ]
        ]);
    }

    /**
     * Get scoring summary for a project
     */
    public function scoringSummary(Project $project)
    {
        $judgmentDetails = JudgmentDetail::with(['criteria.criteriaType'])
                                        ->byProject($project->id)
                                        ->active()
                                        ->get();

        $summary = [
            'total_criteria' => $judgmentDetails->count(),
            'completed_criteria' => $judgmentDetails->whereNotNull('criteria_point')->count(),
            'pending_criteria' => $judgmentDetails->whereNull('criteria_point')->count(),
            'average_score' => $judgmentDetails->whereNotNull('criteria_point')->avg('criteria_point'),
            'total_score' => $judgmentDetails->whereNotNull('criteria_point')->sum('criteria_point'),
            'by_criteria_type' => $judgmentDetails->groupBy('criteria.criteriaType.type_name')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'completed' => $group->whereNotNull('criteria_point')->count(),
                    'average_score' => $group->whereNotNull('criteria_point')->avg('criteria_point'),
                    'total_score' => $group->whereNotNull('criteria_point')->sum('criteria_point')
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Bulk update judgment details
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'judgment_details' => 'required|array',
            'judgment_details.*.id' => 'required|exists:judgment_detail,id',
            'judgment_details.*.criteria_point' => 'nullable|numeric|min:0',
            'judgment_details.*.criteria_percentage' => 'required|integer|min:0|max:100'
        ]);

        \DB::transaction(function () use ($validated) {
            foreach ($validated['judgment_details'] as $detail) {
                JudgmentDetail::where('id', $detail['id'])->update([
                    'criteria_point' => $detail['criteria_point'],
                    'criteria_percentage' => $detail['criteria_percentage']
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Judgment details updated successfully'
        ]);
    }
}
