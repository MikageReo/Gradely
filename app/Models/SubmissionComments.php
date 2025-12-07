<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Submissions;
use App\Models\User;

class SubmissionComments extends Model
{
    protected $fillable = [
        'submission_id',
        'user_id',
        'comment',
    ];

    /**
     * Get the submission that this comment belongs to
     */
    public function submission()
    {
        return $this->belongsTo(Submissions::class, 'submission_id');
    }

    /**
     * Get the user who made this comment
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
