<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_code',
        'name',
        'client_id',
        'project_type_id',
        'project_manager_id',
        'start_date',
        'target_completion',
        'current_phase_id',
        'progress_percentage',
        'status',
        'health_override',
        'priority',
        'description',
        'last_update_at',
        'next_action',
    ];

    // Relasi ke Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relasi ke Project Type
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    // Relasi ke Project Manager (User)
    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    // Relasi ke Project Phase
    public function currentPhase()
    {
        return $this->belongsTo(ProjectPhase::class, 'current_phase_id');
    }
    // Relasi ke Tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function risks()
    {
        return $this->hasMany(\App\Models\Risk::class);
    }

    public function issues()
    {
        return $this->hasMany(\App\Models\Issue::class);
    }

    public function changeRequests()
    {
        return $this->hasMany(\App\Models\ChangeRequest::class);
    }

    public function meetings()
    {
        return $this->hasMany(\App\Models\Meeting::class);
    }
}
