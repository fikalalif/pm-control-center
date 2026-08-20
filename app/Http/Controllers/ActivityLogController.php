<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Data simulasi (dummy) untuk melihat UI Activity Log
        $activities = [
            ['id' => 1, 'user' => 'Admin System', 'action' => 'created a new project', 'target' => 'Project Alpha (PRJ-001)', 'date' => now()->subMinutes(30)->diffForHumans(), 'type' => 'project'],
            ['id' => 2, 'user' => 'John Doe', 'action' => 'completed the task', 'target' => 'Database Migration (TSK-04)', 'date' => now()->subHours(2)->diffForHumans(), 'type' => 'task'],
            ['id' => 3, 'user' => 'Jane Smith', 'action' => 'logged a new risk', 'target' => 'Server Overload (RSK-02)', 'date' => now()->subDays(1)->diffForHumans(), 'type' => 'risk'],
            ['id' => 4, 'user' => 'Admin System', 'action' => 'scheduled a meeting', 'target' => 'Weekly Sync', 'date' => now()->subDays(2)->diffForHumans(), 'type' => 'meeting'],
        ];

        return Inertia::render('ActivityLogs/Index', [
            'activities' => $activities
        ]);
    }
}
