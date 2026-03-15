<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'activity_logs';

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'user_id',
        'activity',
        'duration_minutes',
    ];

    /**
     * Relasi balik ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}