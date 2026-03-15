<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    protected $table = 'student_progress';
    protected $fillable = ['user_id', 'course_id', 'last_score', 'completion_percentage', 'is_at_risk', 'status_adaptif'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
