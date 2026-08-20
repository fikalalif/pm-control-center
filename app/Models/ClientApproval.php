<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientApproval extends Model
{
    protected $fillable = ['project_id', 'title', 'description', 'requested_at', 'due_date', 'status', 'approved_at', 'approved_by', 'notes'];
}
