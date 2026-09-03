<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Risk extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

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
