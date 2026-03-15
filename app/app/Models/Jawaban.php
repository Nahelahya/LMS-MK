<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawabans';

    protected $fillable = [
        'materi_id',
        'student_id',
        'file_path',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
