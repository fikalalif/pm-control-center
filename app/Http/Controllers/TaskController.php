<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:view_tasks', only: ['index', 'show']),
            new Middleware('can:create_tasks', only: ['create', 'store']),
            new Middleware('can:edit_tasks', only: ['edit', 'update']),
            new Middleware('can:delete_tasks', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignedUser', 'vendor'])->latest();

        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('task_code', 'like', "%{$search}%");
            });
        });

        $query->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });

        $tasks = $query->paginate(15)->withQueryString();

        return \Inertia\Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            // Data master untuk dropdown form Global Add Task
            'projects' => \App\Models\Project::select('id', 'name', 'project_code')->get(),
            'users' => \App\Models\User::select('id', 'name')->get(),
            'vendors' => \App\Models\Vendor::select('id', 'name')->get(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_code' => 'required|string|unique:tasks,task_code|max:255',
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'assigned_user_id' => 'nullable|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        Task::create($validated);

        return redirect()->back()->with('message', 'Task created successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->back()->with('message', 'Task deleted successfully.');
    }

    public function show(Task $task)
    {
        $task->load(['project', 'assignedUser', 'vendor']);
        return \Inertia\Inertia::render('Tasks/Show', [
            'task' => $task
        ]);
    }

    public function edit(Task $task)
    {
        return \Inertia\Inertia::render('Tasks/Edit', [
            'task' => $task,
            'projects' => \App\Models\Project::select('id', 'name', 'project_code')->get(),
            'users' => \App\Models\User::select('id', 'name')->get(),
            'vendors' => \App\Models\Vendor::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_code' => 'required|string|max:255|unique:tasks,task_code,' . $task->id,
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_user_id' => 'nullable|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $task->update($validated);

        return redirect()->back()->with('message', 'Task updated successfully.');
    }
}
