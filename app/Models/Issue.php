<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

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
