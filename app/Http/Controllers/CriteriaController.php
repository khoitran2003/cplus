<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Criteria;
use App\Models\CriteriaType;
use App\Models\Industry;
use App\Models\Client;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $criteria = Criteria::with(['criteriaType', 'parent', 'client'])
                          ->active()
                          ->orderBy('parentId', 'asc')
                          ->orderBy('criteria_name', 'asc')
                          ->paginate(15);
        
        $criteriaTypes = CriteriaType::active()->get();
        $clients = Client::all();
        
        return view('admin.criteria.index', compact('criteria', 'criteriaTypes', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $criteriaTypes = CriteriaType::active()->get();
        $clients = Client::all();
        $parentCriteria = Criteria::rootCriteria()->active()->get();
        
        return view('admin.criteria.create', compact('criteriaTypes', 'clients', 'parentCriteria'));
    }

    /**
     * Store a newly created resource in storage.
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

        Criteria::create($validated);

        return redirect()->route('criteria.index')
                        ->with('success', 'Tiêu chí đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
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
        
        return view('admin.criteria.show', compact('criteria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Criteria $criteria)
    {
        $criteriaTypes = CriteriaType::active()->get();
        $clients = Client::all();
        $parentCriteria = Criteria::rootCriteria()->active()->where('id', '!=', $criteria->id)->get();
        
        return view('admin.criteria.edit', compact('criteria', 'criteriaTypes', 'clients', 'parentCriteria'));
    }

    /**
     * Update the specified resource in storage.
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
            return back()->withErrors(['parentId' => 'Không thể chọn chính tiêu chí này làm tiêu chí cha.']);
        }

        $criteria->update($validated);

        return redirect()->route('criteria.index')
                        ->with('success', 'Tiêu chí đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Criteria $criteria)
    {
        // Check if has children
        if ($criteria->children()->count() > 0) {
            return back()->with('error', 'Không thể xóa tiêu chí có tiêu chí con. Vui lòng xóa các tiêu chí con trước.');
        }

        // Check if has judgment details
        if ($criteria->judgmentDetails()->count() > 0) {
            return back()->with('error', 'Không thể xóa tiêu chí đã có dữ liệu đánh giá.');
        }

        $criteria->delete();

        return redirect()->route('criteria.index')
                        ->with('success', 'Tiêu chí đã được xóa thành công.');
    }

    /**
     * Get criteria hierarchy
     */
    public function hierarchy()
    {
        $rootCriteria = Criteria::with(['children.criteriaType'])
                               ->rootCriteria()
                               ->active()
                               ->get();

        return response()->json($rootCriteria);
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

        return response()->json($criteria);
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

        return response()->json($criteria);
    }

    /**
     * Assign criteria to industry
     */
    public function assignToIndustry(Request $request, Criteria $criteria)
    {
        $validated = $request->validate([
            'industry_ids' => 'required|array',
            'industry_ids.*' => 'exists:industry,id'
        ]);

        $criteria->industries()->sync($validated['industry_ids']);

        return back()->with('success', 'Tiêu chí đã được gán cho ngành công nghiệp thành công.');
    }
}