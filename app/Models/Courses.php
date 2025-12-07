<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CourseStudent;
use App\Models\Assignments;

class Courses extends Model
{
    protected $fillable = [
        'course_code',
        'course_name',
        'lecturer_id',
    ];

    /**
     * Get the lecturer (user) who owns this course
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get all students enrolled in this course
     */
    public function courseStudents()
    {
        return $this->hasMany(CourseStudent::class, 'course_id');
    }

    /**
     * Get all students enrolled in this course (many-to-many relationship)
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id');
    }

    /**
     * Get all assignments for this course
     */
    public function assignments()
    {
        return $this->hasMany(Assignments::class, 'course_id');
    }
}
