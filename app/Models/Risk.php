<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    use HasFactory;

    protected $fillable = ['risk_code', 'project_id', 'title', 'description', 'probability', 'impact', 'risk_level', 'mitigation', 'owner_id', 'status', 'due_date'];

    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
