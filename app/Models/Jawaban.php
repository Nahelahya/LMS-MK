<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawabans';

    protected $fillable = [
        'materi_id',   // uuid — sesuai materi.id
        'student_id',  // bigint — sesuai users.id
        'file_path',
    ];

    // materi_id adalah uuid (string), bukan integer
    protected $casts = [
        'materi_id' => 'string',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}