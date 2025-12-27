<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Courses;
use App\Models\User;
use App\Models\Submissions;
use App\Models\AssignmentFile;
use App\Models\CourseLecturer;

class Assignments extends Model
{
    protected $fillable = [
        'course_id',
        'lecturer_id',
        'course_lecturer_id',
        'title',
        'description',
        'attachment',
        'due_date',
        'status',
        'visibility',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Get the course that this assignment belongs to
     */
    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    /**
     * Get the lecturer (user) who created this assignment
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get all submissions for this assignment
     */
    public function submissions()
    {
        return $this->hasMany(Submissions::class, 'assignment_id');
    }

    /**
     * Get all files for this assignment
     */
    public function assignmentFiles()
    {
        return $this->hasMany(AssignmentFile::class, 'assignment_id');
    }

    /**
     * Get the course lecturer (section) that this assignment belongs to
     */
    public function courseLecturer()
    {
        return $this->belongsTo(CourseLecturer::class, 'course_lecturer_id');
    }
}
