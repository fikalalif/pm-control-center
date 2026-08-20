<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProjectExportController extends Controller
{
    public function exportPdf(Project $project)
    {
        $project->load([
            'client',
            'projectManager',
            'tasks.assignedUser',
            'tasks.vendor',
            'milestones',
            'risks',
            'issues',
        ]);

        $pdf = Pdf::loadView('exports.project-pdf', [
            'project' => $project
        ]);

        // You can use download() or stream()
        return $pdf->stream('Project_' . $project->project_code . '_Report.pdf');
    }
}
