<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MilestoneController extends Controller
{
    // Tampilan Global di Sidebar
    public function index()
    {
        $milestones = Milestone::with(['project'])->latest()->paginate(15);

        return Inertia::render('Milestones/Index', [
            'milestones' => $milestones,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'due_date' => 'required|date',
            'status' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Milestone::create($validated);

        return redirect()->back()->with('message', 'Milestone created successfully.');
    }

    public function destroy(Milestone $milestone)
    {
        $milestone->delete();
        return redirect()->back()->with('message', 'Milestone deleted successfully.');
    }

    public function show(Milestone $milestone)
    {
        $milestone->load('project');
        return Inertia::render('Milestones/Show', [
            'milestone' => $milestone
        ]);
    }

    public function edit(Milestone $milestone)
    {
        return Inertia::render('Milestones/Edit', [
            'milestone' => $milestone,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
        ]);
    }

    public function update(Request $request, Milestone $milestone)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'due_date' => 'required|date',
            'status' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $milestone->update($validated);

        return redirect()->back()->with('message', 'Milestone updated successfully.');
    }
}
