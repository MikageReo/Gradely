<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Assignments;

class AssignmentFile extends Model
{
    protected $fillable = [
        'assignment_id',
        'file_path',
        'original_filename',
        'file_type',
        'file_size',
    ];

    /**
     * Get the assignment that this file belongs to
     */
    public function assignment()
    {
        return $this->belongsTo(Assignments::class, 'assignment_id');
    }
}

