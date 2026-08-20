<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Report - {{ $project->project_code }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        h1, h2, h3 { color: #4F46E5; }
        .header { border-bottom: 2px solid #4F46E5; padding-bottom: 10px; margin-bottom: 20px; }
        .meta-data { width: 100%; margin-bottom: 20px; }
        .meta-data td { padding: 5px; }
        .meta-data td strong { display: block; font-size: 10px; color: #666; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f9fafb; font-weight: bold; color: #374151; }
        .status { padding: 3px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Project Report: {{ $project->name }}</h1>
        <p>Code: {{ $project->project_code }} | Generated on: {{ date('Y-m-d H:i') }}</p>
    </div>

    <table class="meta-data">
        <tr>
            <td><strong>Client</strong><br>{{ $project->client->name ?? 'N/A' }}</td>
            <td><strong>Project Manager</strong><br>{{ $project->projectManager->name ?? 'N/A' }}</td>
            <td><strong>Status</strong><br>{{ $project->status }}</td>
            <td><strong>Progress</strong><br>{{ $project->progress_percentage }}%</td>
        </tr>
        <tr>
            <td><strong>Priority</strong><br>{{ $project->priority }}</td>
            <td><strong>Start Date</strong><br>{{ $project->start_date }}</td>
            <td><strong>Deadline</strong><br>{{ $project->target_completion }}</td>
            <td><strong>Budget</strong><br>{{ $project->budget ? number_format($project->budget) : 'N/A' }}</td>
        </tr>
    </table>

    <hr>

    <h3>1. Milestones</h3>
    @if($project->milestones->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->milestones as $ms)
            <tr>
                <td>{{ $ms->name }}</td>
                <td>{{ $ms->due_date }}</td>
                <td>{{ $ms->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No milestones recorded.</p>
    @endif

    <h3>2. Tasks</h3>
    @if($project->tasks->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Assignee / Vendor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->tasks as $task)
            <tr>
                <td>{{ $task->task_code }}</td>
                <td>{{ $task->name }}</td>
                <td>
                    {{ $task->assignedUser->name ?? '' }}
                    {{ $task->vendor ? ' (Vendor: '.$task->vendor->name.')' : '' }}
                    @if(!$task->assignedUser && !$task->vendor) - @endif
                </td>
                <td>{{ $task->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No tasks recorded.</p>
    @endif

    <h3>3. Risks</h3>
    @if($project->risks->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Risk Code</th>
                <th>Title</th>
                <th>Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->risks as $risk)
            <tr>
                <td>{{ $risk->risk_code }}</td>
                <td>{{ $risk->title }}</td>
                <td>{{ $risk->risk_level }}</td>
                <td>{{ $risk->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No risks recorded.</p>
    @endif

    <h3>4. Issues</h3>
    @if($project->issues->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Issue Code</th>
                <th>Title</th>
                <th>Impact</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->issues as $issue)
            <tr>
                <td>{{ $issue->issue_code }}</td>
                <td>{{ $issue->title }}</td>
                <td>{{ $issue->impact }}</td>
                <td>{{ $issue->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No issues recorded.</p>
    @endif

</body>
</html>
