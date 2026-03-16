<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasUuids;

    protected $table = 'materi';

    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
        'tipe_file',
        'uploaded_by'
    ];
    protected $keyType = 'string';
    public $incrementing = false;
}