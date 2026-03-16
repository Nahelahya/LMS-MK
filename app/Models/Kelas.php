<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'staff_id',
        'nama_kelas',
        'mata_pelajaran',
        'kode_kelas',
        'deskripsi',
    ];

    // Generate kode unik otomatis saat membuat kelas baru
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($kelas) {
            if (empty($kelas->kode_kelas)) {
                $kelas->kode_kelas = strtoupper(Str::random(6));
            }
        });
    }

    // Staff/guru pemilik kelas
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // Siswa yang join kelas ini
    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kelas_siswa', 'kelas_id', 'user_id')
            ->withTimestamps();
    }

    // Course/materi di dalam kelas ini
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'kelas_id');
    }

    // Jumlah siswa
    public function getJumlahSiswaAttribute(): int
    {
        return $this->siswa()->count();
    }
}