<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorDeliverable extends Model
{
    protected $fillable = ['vendor_id', 'project_id', 'name', 'description', 'assigned_user_id', 'start_date', 'deadline', 'progress_percentage', 'status', 'quality_status', 'pending_revision', 'last_follow_up_at', 'next_follow_up_at'];
}
