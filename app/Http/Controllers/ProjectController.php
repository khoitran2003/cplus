<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\Location;
use App\Models\ProjectLocation;
use App\Models\Criteria;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with(['client', 'projectLocations.location'])
                          ->latest()
                          ->paginate(10);
        
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        return view('admin.projects.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'industry' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,completed'
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
                        ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['client', 'projectLocations.location', 'projectLocations.scores.criteria']);
        
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $clients = Client::all();
        return view('admin.projects.edit', compact('project', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'industry' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,completed'
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
                        ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
                        ->with('success', 'Project deleted successfully.');
    }

    /**
     * Show locations for a project
     */
    public function locations(Project $project)
    {
        $assignedLocations = $project->projectLocations()->with('location')->get();
        $availableLocations = Location::where('is_active', true)
                                    ->whereNotIn('id', $assignedLocations->pluck('location_id'))
                                    ->get();

        return view('admin.projects.locations', compact('project', 'assignedLocations', 'availableLocations'));
    }

    /**
     * Assign location to project
     */
    public function assignLocation(Request $request, Project $project)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id'
        ]);

        // Check if location is already assigned
        if ($project->projectLocations()->where('location_id', $validated['location_id'])->exists()) {
            return back()->with('error', 'Location already assigned to this project.');
        }

        $project->projectLocations()->create([
            'location_id' => $validated['location_id']
        ]);

        return back()->with('success', 'Location assigned successfully.');
    }

    /**
     * Remove location from project
     */
    public function removeLocation(Project $project, Location $location)
    {
        $project->projectLocations()->where('location_id', $location->id)->delete();

        return back()->with('success', 'Location removed from project.');
    }

    /**
     * Show scoring interface for project
     */
    public function scoring(Project $project)
    {
        $project->load(['projectLocations.location', 'projectLocations.scores.criteria']);
        $criteria = Criteria::where('is_active', true)->orderBy('sort_order')->get();

        // Calculate total scores if not already calculated
        foreach ($project->projectLocations as $projectLocation) {
            if ($projectLocation->total_score === null) {
                $projectLocation->calculateTotalScore();
            }
        }

        // Sort by total score descending
        $project->projectLocations = $project->projectLocations->sortByDesc('total_score')->values();

        return view('admin.projects.scoring', compact('project', 'criteria'));
    }

    /**
     * Save scoring data
     */
    public function saveScoring(Request $request, Project $project)
    {
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*.project_location_id' => 'required|exists:project_locations,id',
            'scores.*.criteria_id' => 'required|exists:criteria,id',
            'scores.*.score' => 'required|numeric|min:0',
            'scores.*.notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['scores'] as $scoreData) {
                Score::updateOrCreate(
                    [
                        'project_location_id' => $scoreData['project_location_id'],
                        'criteria_id' => $scoreData['criteria_id']
                    ],
                    [
                        'score' => $scoreData['score'],
                        'notes' => $scoreData['notes'] ?? null
                    ]
                );
            }

            // Recalculate total scores for all project locations
            foreach ($validated['scores'] as $scoreData) {
                $projectLocation = ProjectLocation::find($scoreData['project_location_id']);
                if ($projectLocation) {
                    $projectLocation->calculateTotalScore();
                }
            }
        });

        return back()->with('success', 'Scores saved successfully.');
    }

    /**
     * Export project results
     */
    public function export(Project $project, $format = 'pdf')
    {
        $project->load(['client', 'projectLocations.location', 'projectLocations.scores.criteria']);
        
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
        return response()->json(['message' => 'PDF export not implemented yet']);
    }

    private function exportToExcel(Project $project)
    {
        // TODO: Implement Excel export using a package like maatwebsite/excel
        return response()->json(['message' => 'Excel export not implemented yet']);
    }
}
