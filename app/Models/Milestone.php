<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Milestone extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = ['milestone_code', 'project_id', 'name', 'description', 'due_date', 'status', 'progress_percentage', 'assigned_user_id', 'dependency_milestone_id', 'completed_at'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
