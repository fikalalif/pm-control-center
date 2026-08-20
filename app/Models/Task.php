<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['task_code', 'project_id', 'name', 'description', 'assigned_user_id', 'vendor_id', 'start_date', 'deadline', 'status', 'progress_percentage', 'dependency_task_id', 'completed_at'];

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relasi ke User (PIC)
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    // Relasi ke Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
