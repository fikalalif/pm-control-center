<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChangeRequestController extends Controller
{
    public function index()
    {
        $changeRequests = ChangeRequest::with(['project', 'approvedBy'])->latest()->paginate(15);

        return Inertia::render('ChangeRequests/Index', [
            'changeRequests' => $changeRequests,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
            'users' => User::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'cr_code' => 'required|string|unique:change_requests,cr_code|max:255',
            'description' => 'required|string',
            'requested_by' => 'nullable|string',
            'impact' => 'nullable|string',
            'status' => 'required|string',
        ]);

        ChangeRequest::create($validated);

        return redirect()->back()->with('message', 'Change Request logged successfully.');
    }

    public function destroy(ChangeRequest $changeRequest)
    {
        $changeRequest->delete();
        return redirect()->back()->with('message', 'Change Request deleted successfully.');
    }

    public function show(ChangeRequest $changeRequest)
    {
        $changeRequest->load(['project', 'approvedBy']);
        return Inertia::render('ChangeRequests/Show', [
            'changeRequest' => $changeRequest
        ]);
    }

    public function edit(ChangeRequest $changeRequest)
    {
        return Inertia::render('ChangeRequests/Edit', [
            'changeRequest' => $changeRequest,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
            'users' => User::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, ChangeRequest $changeRequest)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'cr_code' => 'required|string|max:255|unique:change_requests,cr_code,' . $changeRequest->id,
            'description' => 'required|string',
            'requested_by' => 'nullable|string',
            'impact' => 'nullable|string',
            'status' => 'required|string',
            'decision_date' => 'nullable|date',
            'decision_notes' => 'nullable|string',
            'approved_by' => 'nullable|exists:users,id',
        ]);

        $changeRequest->update($validated);

        return redirect()->back()->with('message', 'Change Request updated successfully.');
    }
}
