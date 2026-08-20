<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Risk;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung statistik utama untuk Dashboard
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::whereNotIn('status', ['Completed', 'Cancelled'])->count(),
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::where('status', 'Pending')->count(),
            'critical_risks' => Risk::whereIn('risk_level', ['High', 'Critical'])->where('status', 'Open')->count(),
            'upcoming_milestones' => Milestone::where('status', 'Pending')->whereDate('due_date', '>=', now())->count(),
        ];

        // Ambil 5 Project terbaru untuk tabel Quick View
        $recent_projects = Project::with(['client', 'projectManager'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recent_projects' => $recent_projects
        ]);
    }
}
