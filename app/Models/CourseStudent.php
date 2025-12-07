<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Courses;
use App\Models\User;

class CourseStudent extends Model
{
    protected $table = 'course_student';

    protected $fillable = [
        'course_id',
        'student_id',
    ];

    /**
     * Get the course that this enrollment belongs to
     */
    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    /**
     * Get the student (user) enrolled in the course
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
