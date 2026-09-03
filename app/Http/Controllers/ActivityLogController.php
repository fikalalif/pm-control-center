<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate(15);

        return Inertia::render('ActivityLogs/Index', [
            'activities' => $activities
        ]);
    }
}
