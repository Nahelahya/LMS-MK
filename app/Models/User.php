<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Kelas;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    public function progress()
    {
        return $this->hasOne(StudentProgress::class, 'user_id');
    }
    public function activities()
    {
        return $this->hasMany(ActivityLog::class , 'user_id');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'status',
        'role',
        'password',
        'otp',
        'provider_id',
        'provider_name',
        'github_id',
        'avatar',
        'photo',
        'language',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function kelasDimiliki()
{
    return $this->hasMany(Kelas::class, 'staff_id');
}

// Relasi: kelas yang DIIKUTI siswa (sebagai peserta)
public function kelas()
{
    return $this->belongsToMany(Kelas::class, 'kelas_siswa', 'user_id', 'kelas_id')
        ->withTimestamps();

        }
            public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/profile/' . $this->photo);
        }

        // Gunakan UI Avatars sebagai fallback
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
            . '&background=6366f1&color=fff&size=128';
    }
}