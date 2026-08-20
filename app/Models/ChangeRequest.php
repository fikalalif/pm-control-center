<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = ['cr_code', 'project_id', 'description', 'requested_by', 'requested_at', 'impact', 'status', 'decision_date', 'decision_notes', 'approved_by'];

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
