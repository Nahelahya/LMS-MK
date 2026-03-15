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
        'original_name',   // <-- NEW: original filename for clean downloads
        'tipe_file',
        'uploaded_by',
    ];

    // Relationship: who uploaded this material
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
