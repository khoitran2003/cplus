<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Criteria;
use App\Models\CriteriaType;
use App\Models\Industry;
use App\Models\Client;
use Illuminate\Http\Request;

class CriteriaApiController extends Controller
{
    /**
     * Get all criteria with hierarchy
     */
    public function index(Request $request)
    {
        $query = Criteria::with(['criteriaType', 'parent', 'client'])
                        ->active();

        // Filter by type
        if ($request->has('type_id')) {
            $query->byType($request->type_id);
        }

        // Filter by client
        if ($request->has('client_id')) {
            $query->byClient($request->client_id);
        }

        // Filter by industry
        if ($request->has('industry_id')) {
            $query->whereHas('industries', function($q) use ($request) {
                $q->where('industry.id', $request->industry_id);
            });
        }

        // Include hierarchy
        if ($request->has('hierarchy') && $request->hierarchy) {
            $query->with(['children.criteriaType']);
        }

        $criteria = $query->orderBy('parentId', 'asc')
                         ->orderBy('criteria_name', 'asc')
                         ->get();

        return response()->json([
            'success' => true,
            'data' => $criteria
        ]);
    }

    /**
     * Get criteria hierarchy (root criteria with children)
     */
    public function hierarchy()
    {
        $rootCriteria = Criteria::with(['children.criteriaType'])
                               ->rootCriteria()
                               ->active()
                               ->get();

        return response()->json([
            'success' => true,
            'data' => $rootCriteria
        ]);
    }

    /**
     * Get criteria by type
     */
    public function byType(CriteriaType $criteriaType)
    {
        $criteria = Criteria::with(['criteriaType', 'parent'])
                           ->byType($criteriaType->id)
                           ->active()
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $criteria
        ]);
    }

    /**
     * Get criteria by client
     */
    public function byClient(Client $client)
    {
        $criteria = Criteria::with(['criteriaType', 'parent'])
                           ->byClient($client->id)
                           ->active()
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $criteria
        ]);
    }

    /**
     * Get criteria by industry
     */
    public function byIndustry(Industry $industry)
    {
        $criteria = Criteria::with(['criteriaType', 'parent'])
                           ->whereHas('industries', function($query) use ($industry) {
                               $query->where('industry.id', $industry->id);
                           })
                           ->active()
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $criteria
        ]);
    }

    /**
     * Get single criteria with full details
     */
    public function show(Criteria $criteria)
    {
        $criteria->load([
            'criteriaType',
            'parent',
            'children.criteriaType',
            'client',
            'judgmentDetails.project',
            'industries'
        ]);

        return response()->json([
            'success' => true,
            'data' => $criteria
        ]);
    }

    /**
     * Create new criteria
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'criteria_name' => 'required|string|max:255',
            'criteriaTypeId' => 'nullable|exists:criteria_type,id',
            'parentId' => 'nullable|exists:criteria,id',
            'clientId' => 'nullable|exists:clients,id',
            'criteriaPercent' => 'nullable|numeric|min:0|max:100'
        ]);

        $criteria = Criteria::create($validated);
        $criteria->load(['criteriaType', 'parent', 'client']);

        return response()->json([
            'success' => true,
            'message' => 'Criteria created successfully',
            'data' => $criteria
        ], 201);
    }

    /**
     * Update criteria
     */
    public function update(Request $request, Criteria $criteria)
    {
        $validated = $request->validate([
            'criteria_name' => 'required|string|max:255',
            'criteriaTypeId' => 'nullable|exists:criteria_type,id',
            'parentId' => 'nullable|exists:criteria,id',
            'clientId' => 'nullable|exists:clients,id',
            'criteriaPercent' => 'nullable|numeric|min:0|max:100'
        ]);

        // Prevent setting parent to self or descendant
        if ($validated['parentId'] == $criteria->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot set parent to self'
            ], 400);
        }

        $criteria->update($validated);
        $criteria->load(['criteriaType', 'parent', 'client']);

        return response()->json([
            'success' => true,
            'message' => 'Criteria updated successfully',
            'data' => $criteria
        ]);
    }

    /**
     * Delete criteria
     */
    public function destroy(Criteria $criteria)
    {
        // Check if has children
        if ($criteria->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete criteria with children'
            ], 400);
        }

        // Check if has judgment details
        if ($criteria->judgmentDetails()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete criteria with judgment details'
            ], 400);
        }

        $criteria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Criteria deleted successfully'
        ]);
    }

    /**
     * Assign criteria to industries
     */
    public function assignToIndustries(Request $request, Criteria $criteria)
    {
        $validated = $request->validate([
            'industry_ids' => 'required|array',
            'industry_ids.*' => 'exists:industry,id'
        ]);

        $criteria->industries()->sync($validated['industry_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Criteria assigned to industries successfully',
            'data' => $criteria->load('industries')
        ]);
    }

    /**
     * Get criteria types
     */
    public function types()
    {
        $types = CriteriaType::active()->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Get industries
     */
    public function industries()
    {
        $industries = Industry::active()->get();

        return response()->json([
            'success' => true,
            'data' => $industries
        ]);
    }
}
