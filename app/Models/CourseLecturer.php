<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Courses;
use App\Models\User;
use App\Models\CourseStudent;
use App\Models\Assignments;

class CourseLecturer extends Model
{
    protected $table = 'course_lecturer';

    protected $fillable = [
        'course_id',
        'lecturer_id',
        'section',
        'capacity',
    ];

    /**
     * Get the course that this lecturer teaches
     */
    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    /**
     * Get the lecturer (user) who teaches this course
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get all students enrolled in this course section
     */
    public function students()
    {
        return $this->hasMany(CourseStudent::class, 'course_lecturer_id');
    }

    /**
     * Get all assignments for this course
     */
    public function assignments()
    {
        return $this->hasMany(Assignments::class, 'course_id', 'course_id');
    }
}
