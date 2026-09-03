<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MilestoneController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:view_milestones', only: ['index', 'show']),
            new Middleware('can:create_milestones', only: ['create', 'store']),
            new Middleware('can:edit_milestones', only: ['edit', 'update']),
            new Middleware('can:delete_milestones', only: ['destroy']),
        ];
    }

    // Tampilan Global di Sidebar
    public function index(Request $request)
    {
        $query = Milestone::with(['project'])->latest();

        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('milestone_code', 'like', "%{$search}%");
            });
        });

        $query->when($request->filter === 'upcoming', function ($q) {
            $q->where('status', 'Pending')->whereDate('due_date', '>=', now());
        });

        $query->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        $milestones = $query->paginate(15)->withQueryString();

        return Inertia::render('Milestones/Index', [
            'milestones' => $milestones,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
            'filters' => $request->only(['search', 'status', 'filter']),
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
