<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'type', 'title', 'description', 'owner_id', 'meeting_date', 'due_date', 'status'];
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
