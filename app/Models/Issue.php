<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Issue extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = ['issue_code', 'project_id', 'title', 'description', 'impact', 'owner_id', 'action', 'deadline', 'status', 'resolved_at'];
    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
