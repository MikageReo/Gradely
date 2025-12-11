<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Courses;
use App\Models\Submissions;
use App\Models\SubmissionComments;
use App\Models\CourseLecturer;
use App\Models\CourseStudent;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    /**
     * Get course lecturer assignments (sections) where user is the lecturer
     */
    public function lecturerCourseSections()
    {
        return $this->hasMany(CourseLecturer::class, 'lecturer_id');
    }

    /**
     * Get courses where user is the lecturer (through course_lecturer)
     */
    public function lecturerCourses()
    {
        return $this->belongsToMany(Courses::class, 'course_lecturer', 'lecturer_id', 'course_id')
            ->withPivot('section', 'capacity')
            ->withTimestamps();
    }

    /**
     * Get course student enrollments (sections) where user is enrolled as a student
     */
    public function studentCourseSections()
    {
        return $this->hasMany(CourseStudent::class, 'student_id');
    }

    /**
     * Get courses where user is enrolled as a student (through course_lecturer -> course_student)
     */
    public function studentCourses()
    {
        $courseLecturerIds = CourseStudent::where('student_id', $this->id)
            ->pluck('course_lecturer_id');
        
        $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
            ->pluck('course_id')
            ->unique();
        
        return Courses::whereIn('id', $courseIds);
    }

    /**
     * Get all submissions made by the user (as a student)
     */
    public function submissions()
    {
        return $this->hasMany(Submissions::class, 'student_id');
    }

    /**
     * Get all comments made by the user
     */
    public function submissionComments()
    {
        return $this->hasMany(SubmissionComments::class, 'user_id');
    }
}
