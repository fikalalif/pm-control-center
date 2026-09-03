<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class IssueController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:view_issues', only: ['index', 'show']),
            new Middleware('can:create_issues', only: ['create', 'store']),
            new Middleware('can:edit_issues', only: ['edit', 'update']),
            new Middleware('can:delete_issues', only: ['destroy']),
        ];
    }

    public function index()
    {
        $issues = Issue::with(['project', 'owner'])->latest()->paginate(15);

        return Inertia::render('Issues/Index', [
            'issues' => $issues,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
            'users' => User::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'issue_code' => 'required|string|unique:issues,issue_code|max:255',
            'title' => 'required|string|max:255',
            'impact' => 'nullable|string|max:255',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'action' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        Issue::create($validated);

        return redirect()->back()->with('message', 'Issue logged successfully.');
    }

    public function destroy(Issue $issue)
    {
        $issue->delete();
        return redirect()->back()->with('message', 'Issue deleted successfully.');
    }

    public function show(Issue $issue)
    {
        $issue->load(['project', 'owner']);
        return Inertia::render('Issues/Show', [
            'issue' => $issue
        ]);
    }

    public function edit(Issue $issue)
    {
        return Inertia::render('Issues/Edit', [
            'issue' => $issue,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
            'users' => User::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'issue_code' => 'required|string|max:255|unique:issues,issue_code,' . $issue->id,
            'title' => 'required|string|max:255',
            'impact' => 'nullable|string|max:255',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'action' => 'nullable|string',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $issue->update($validated);

        return redirect()->back()->with('message', 'Issue updated successfully.');
    }
}
