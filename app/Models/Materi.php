<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
        'tipe_file',
        'uploaded_by'
    ];
}