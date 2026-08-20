<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Relasi ke tabel Projects
    public function projects()
    {
        return $this->hasMany(Project::class, 'current_phase_id');
    }
}
