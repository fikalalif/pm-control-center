<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_code',
        'name',
        'contact_person',
        'email',
        'phone',
        'industry',
        'notes',
    ];

    // Relasi ke tabel Projects (Akan kita gunakan nanti)
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
