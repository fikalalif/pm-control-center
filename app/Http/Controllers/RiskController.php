<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RiskController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:view_risks', only: ['index', 'show']),
            new Middleware('can:create_risks', only: ['create', 'store']),
            new Middleware('can:edit_risks', only: ['edit', 'update']),
            new Middleware('can:delete_risks', only: ['destroy']),
        ];
    }

    // Tampilan Global di Sidebar
    public function index(Request $request)
    {
        $query = Risk::with(['project', 'owner'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('risk_code', 'like', "%{$search}%");
            });
        }

        $riskLevel = $request->input('risk_level') ?? $request->input('severity');
        if ($riskLevel) {
            $query->where('risk_level', $riskLevel);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $risks = $query->paginate(15)->withQueryString();

        return Inertia::render('Risks/Index', [
            'risks' => $risks,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
            'users' => User::select('id', 'name')->get(),
            'filters' => [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'risk_level' => $riskLevel,
                'severity' => $riskLevel,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'risk_code' => 'required|string|unique:risks,risk_code|max:255',
            'title' => 'required|string|max:255',
            'risk_level' => 'required|string|in:Low,Medium,High,Critical',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'mitigation' => 'nullable|string',
        ]);

        Risk::create($validated);

        return redirect()->back()->with('message', 'Risk logged successfully.');
    }

    public function destroy(Risk $risk)
    {
        $risk->delete();
        return redirect()->back()->with('message', 'Risk deleted successfully.');
    }

    public function show(Risk $risk)
    {
        $risk->load(['project', 'owner']);
        return Inertia::render('Risks/Show', [
            'risk' => $risk
        ]);
    }

    public function edit(Risk $risk)
    {
        return Inertia::render('Risks/Edit', [
            'risk' => $risk,
            'projects' => Project::select('id', 'name', 'project_code')->get(),
            'users' => User::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Risk $risk)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'risk_code' => 'required|string|max:255|unique:risks,risk_code,' . $risk->id,
            'title' => 'required|string|max:255',
            'risk_level' => 'required|string|in:Low,Medium,High,Critical',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'mitigation' => 'nullable|string',
            'description' => 'nullable|string',
            'probability' => 'nullable|string',
            'impact' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $risk->update($validated);

        return redirect()->back()->with('message', 'Risk updated successfully.');
    }
}
