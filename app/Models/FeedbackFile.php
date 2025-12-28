<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Submissions;

class FeedbackFile extends Model
{
    protected $fillable = [
        'submission_id',
        'file_path',
        'original_filename',
        'file_type',
        'file_size',
    ];

    /**
     * Get the submission that this feedback file belongs to
     */
    public function submission()
    {
        return $this->belongsTo(Submissions::class, 'submission_id');
    }
}

