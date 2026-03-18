<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['user_id', 'kelas_id', 'tanggal', 'status', 'keterangan'];

    protected $casts = ['tanggal' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    public function siswa()
    {
    return $this->belongsToMany(User::class, 'kelas_siswa', 'kelas_id', 'user_id');
    }
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}