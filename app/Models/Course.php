<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Nama tabel di database Supabase kamu
    protected $table = 'courses';

    // Kolom yang boleh diisi
    protected $fillable = [
        'nama_course',
        'kode_course',
        'deskripsi',
    ];
}