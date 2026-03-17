<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Materi extends Model
{
    protected $table = 'materi';

    // UUID primary key
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'kelas_id',       // bigint
        'judul',          // text
        'deskripsi',      // text
        'deadline',       // date
        'file_path',      // text
        'original_name',  // varchar
        'tipe_file',      // text
        'kunci_jawaban',  // text — opsional, kunci jawaban untuk staff
        'uploaded_by',    // bigint (users.id)
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Auto-generate UUID saat create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function jawabans()
    {
        return $this->hasMany(Jawaban::class, 'materi_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }

    public function getDaysLeftAttribute(): ?int
    {
        if (!$this->deadline) return null;
        return (int) now()->startOfDay()->diffInDays($this->deadline, false);
    }
}