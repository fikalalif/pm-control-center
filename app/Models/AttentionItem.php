<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttentionItem extends Model
{
    protected $fillable = ['project_id', 'problem', 'pic', 'deadline', 'recommended_action', 'status'];
}
