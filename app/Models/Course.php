<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'kelas_id',
        'nama_course',
        'kode_course',  
        'deskripsi',    

    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(StudentProgress::class, 'course_id');
    }
}