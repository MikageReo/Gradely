<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Assignments;
use App\Models\User;
use App\Models\SubmissionComments;
use App\Models\SubmissionFile;
use App\Models\FeedbackFile;

class Submissions extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_path',
        'submitted_at',
        'status',
        'score',
        'grade',
        'lecturer_feedback',
        'marked_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'marked_at' => 'datetime',
        'score' => 'double',
    ];

    /**
     * Get the assignment that this submission belongs to
     */
    public function assignment()
    {
        return $this->belongsTo(Assignments::class, 'assignment_id');
    }

    /**
     * Get the student (user) who made this submission
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get all comments for this submission
     */
    public function submissionComments()
    {
        return $this->hasMany(SubmissionComments::class, 'submission_id');
    }

    /**
     * Get all files for this submission
     */
    public function submissionFiles()
    {
        return $this->hasMany(SubmissionFile::class, 'submission_id');
    }

    /**
     * Get all feedback files for this submission
     */
    public function feedbackFiles()
    {
        return $this->hasMany(FeedbackFile::class, 'submission_id');
    }
}
