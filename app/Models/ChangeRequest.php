<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = ['cr_code', 'project_id', 'description', 'requested_by', 'requested_at', 'impact', 'status', 'decision_date', 'decision_notes', 'approved_by'];

    // 1. Tambahkan relasi requester ke tabel User
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    // 2. Sekalian pastiin relasi project-nya ada biar nggak error di halaman lain
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
