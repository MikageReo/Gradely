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
    ];

    /**
     * Get all lecturers teaching this course (through course_lecturer)
     */
    public function courseLecturers()
    {
        return $this->hasMany(CourseLecturer::class, 'course_id');
    }

    /**
     * Get all lecturers (users) teaching this course
     */
    public function lecturers()
    {
        return $this->belongsToMany(User::class, 'course_lecturer', 'course_id', 'lecturer_id')
            ->withPivot('section', 'capacity')
            ->withTimestamps();
    }

    /**
     * Get all students enrolled in this course (through course_lecturer -> course_student)
     */
    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            CourseStudent::class,
            'course_lecturer_id', // Foreign key on course_student table
            'id', // Foreign key on users table
            'id', // Local key on courses table
            'student_id' // Local key on course_student table
        )->distinct();
    }

    /**
     * Get all assignments for this course
     */
    public function assignments()
    {
        return $this->hasMany(Assignments::class, 'course_id');
    }
}
