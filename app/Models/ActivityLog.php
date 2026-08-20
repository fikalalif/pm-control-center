<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'project_id', 'action', 'subject_type', 'subject_id', 'old_values', 'new_values'];
}
