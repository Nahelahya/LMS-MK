<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mata_pelajaran',
        'judul',
        'tipe',
        'nilai',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGradeAttribute(): string
    {
        return match(true) {
            $this->nilai >= 85 => 'A',
            $this->nilai >= 75 => 'B',
            $this->nilai >= 65 => 'C',
            $this->nilai >= 55 => 'D',
            default            => 'E',
        };
    }
}
