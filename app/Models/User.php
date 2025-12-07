<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Courses;
use App\Models\Submissions;
use App\Models\SubmissionComments;

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
     * Get courses where user is the lecturer
     */
    public function lecturerCourses()
    {
        return $this->hasMany(Courses::class, 'lecturer_id');
    }

    /**
     * Get courses where user is enrolled as a student
     */
    public function studentCourses()
    {
        return $this->belongsToMany(Courses::class, 'course_student', 'student_id', 'course_id');
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
