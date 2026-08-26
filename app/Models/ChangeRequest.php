<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    protected $fillable = ['cr_code', 'project_id', 'description', 'requested_by', 'requested_at', 'impact', 'status', 'decision_date', 'decision_notes', 'approved_by'];

    public function requester()
    {
        return $this->belongsTo(\App\Models\User::class, 'requester_id');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    // Tambahkan relasi ini bro
    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
