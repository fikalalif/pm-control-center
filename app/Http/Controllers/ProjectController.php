<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class ProjectController extends Controller implements HasMiddleware
{
    // Middleware permissions
    public static function middleware(): array
    {
        return [
            new Middleware('can:view_projects', only: ['index', 'show']),
            new Middleware('can:create_projects', only: ['create', 'store']),
            new Middleware('can:edit_projects', only: ['edit', 'update']),
            new Middleware('can:delete_projects', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        $query = Project::with(['client', 'projectManager', 'currentPhase', 'projectType'])->latest();

        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('project_code', 'like', "%{$search}%");
            });
        });

        $query->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        $projects = $query->paginate(10)->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        // Kirim semua master data ke form frontend untuk dropdown
        return Inertia::render('Projects/Create', [
            'clients' => Client::select('id', 'name')->get(),
            'users' => User::where('is_active', true)->select('id', 'name')->get(), // Hanya user aktif
            'types' => ProjectType::select('id', 'name')->get(),
            'phases' => ProjectPhase::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Validasi ketat di sisi server sesuai PRD
        $validated = $request->validate([
            'project_code' => 'required|string|unique:projects,project_code|max:255',
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'project_type_id' => 'nullable|exists:project_types,id',
            'project_manager_id' => 'required|exists:users,id',
            'current_phase_id' => 'nullable|exists:project_phases,id',
            'start_date' => 'nullable|date',
            'target_completion' => 'required|date|after_or_equal:start_date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'priority' => 'required|string|in:Low,Medium,High,Critical',
            'status' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Default value jika kosong
        $validated['progress_percentage'] = $validated['progress_percentage'] ?? 0;

        Project::create($validated);

        return redirect()->route('projects.index')->with('message', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        // Kirim data project yang mau diedit beserta semua master data untuk dropdown
        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'clients' => Client::select('id', 'name')->get(),
            'users' => User::where('is_active', true)->select('id', 'name')->get(),
            'types' => ProjectType::select('id', 'name')->get(),
            'phases' => ProjectPhase::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        // Validasi mirip dengan store, tapi project_code mengecualikan ID yang sedang diedit
        $validated = $request->validate([
            'project_code' => 'required|string|max:255|unique:projects,project_code,' . $project->id,
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'project_type_id' => 'nullable|exists:project_types,id',
            'project_manager_id' => 'required|exists:users,id',
            'current_phase_id' => 'nullable|exists:project_phases,id',
            'start_date' => 'nullable|date',
            'target_completion' => 'required|date|after_or_equal:start_date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'priority' => 'required|string|in:Low,Medium,High,Critical',
            'status' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('message', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        // Akan otomatis tertangani sebagai Soft Delete
        $project->delete();

        return redirect()->back()->with('message', 'Project deleted successfully.');
    }

    public function show(Project $project)
    {
        $project->load([
            'client',
            'projectManager',
            'currentPhase',
            'projectType',
            'tasks.assignedUser',
            'tasks.vendor',
            'milestones',
            'risks.owner',
            'issues.owner',
            'changeRequests.requester', // <-- Tambahkan baris ini
        ]);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'users' => User::select('id', 'name')->get(),
            'vendors' => \App\Models\Vendor::select('id', 'name')->get(),
        ]);
    }
}
