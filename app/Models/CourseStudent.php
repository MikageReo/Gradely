<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Courses;
use App\Models\User;
use App\Models\CourseLecturer;

class CourseStudent extends Model
{
    protected $table = 'course_student';

    protected $fillable = [
        'course_lecturer_id',
        'student_id',
    ];

    /**
     * Get the course lecturer (section) that this enrollment belongs to
     */
    public function courseLecturer()
    {
        return $this->belongsTo(CourseLecturer::class, 'course_lecturer_id');
    }

    /**
     * Get the course through course_lecturer
     */
    public function course()
    {
        return $this->hasOneThrough(
            Courses::class,
            CourseLecturer::class,
            'id', // Foreign key on course_lecturer table
            'id', // Foreign key on courses table
            'course_lecturer_id', // Local key on course_student table
            'course_id' // Local key on course_lecturer table
        );
    }

    /**
     * Get the student (user) enrolled in the course
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
