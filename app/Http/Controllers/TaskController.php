<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Ambil semua task dari seluruh project, include relasinya
        $tasks = Task::with(['project', 'assignedUser', 'vendor'])
            ->latest()
            ->paginate(15);

        return \Inertia\Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            // Data master untuk dropdown form Global Add Task
            'projects' => \App\Models\Project::select('id', 'name', 'project_code')->get(),
            'users' => \App\Models\User::select('id', 'name')->get(),
            'vendors' => \App\Models\Vendor::select('id', 'name')->get(),
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
