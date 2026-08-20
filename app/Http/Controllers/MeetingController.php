<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::with(['project'])->latest()->paginate(15);

        return Inertia::render('Meetings/Index', [
            'meetings' => $meetings,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'meeting_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        Meeting::create($validated);

        return redirect()->back()->with('message', 'Meeting scheduled successfully.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->back()->with('message', 'Meeting deleted successfully.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load('project');
        return Inertia::render('Meetings/Show', [
            'meeting' => $meeting
        ]);
    }

    public function edit(Meeting $meeting)
    {
        return Inertia::render('Meetings/Edit', [
            'meeting' => $meeting,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
            'users' => \App\Models\User::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'meeting_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $meeting->update($validated);

        return redirect()->back()->with('message', 'Meeting updated successfully.');
    }
}
