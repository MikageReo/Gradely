<?php

namespace App\Http\Controllers;

use App\Models\Assignments;
use App\Models\Submissions;
use App\Models\SubmissionFile;
use App\Models\SubmissionComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Display the assignment submission page
     */
    public function show($assignmentId)
    {
        $assignment = Assignments::with(['course', 'lecturer'])
            ->findOrFail($assignmentId);

        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'student') {
            $courseIds = $user->studentCourses()->pluck('courses.id');
            if (!$courseIds->contains($assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }
        } elseif ($user->role === 'lecturer') {
            // Check if lecturer is the creator of this assignment
            if ($assignment->lecturer_id !== $user->id) {
                abort(403, 'You are not authorized to view this assignment.');
            }
        }

        // Get existing submission
        // For students: get their own submission
        // For lecturers: get the first submission (or we could add student_id parameter later)
        if ($user->role === 'student') {
            $submission = Submissions::with(['submissionFiles', 'submissionComments.user'])
                ->where('assignment_id', $assignmentId)
                ->where('student_id', $user->id)
                ->first();
        } else {
            // For lecturers, get the first submission for this assignment
            // In a full implementation, you might want to add a student_id parameter
            $submission = Submissions::with(['submissionFiles', 'submissionComments.user', 'student'])
                ->where('assignment_id', $assignmentId)
                ->first();
        }

        return view('assignment_submission', [
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    /**
     * Store a new submission or update existing one
     */
    public function store(Request $request, $assignmentId)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:pdf,doc,docx,txt,rtf,odt|max:10240', // 10MB max per file
        ]);

        $assignment = Assignments::findOrFail($assignmentId);
        $user = Auth::user();

        // Check permissions
        if ($user->role === 'student') {
            $courseIds = $user->studentCourses()->pluck('courses.id');
            if (!$courseIds->contains($assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }
        } elseif ($user->role !== 'admin') {
            // Only students and admins can submit
            abort(403, 'Only students can submit assignments.');
        }

        // Check if submission already exists
        $submission = Submissions::where('assignment_id', $assignmentId)
            ->where('student_id', $user->id)
            ->first();

        if ($submission) {
            // Update existing submission
            $submission->submitted_at = now();
            $submission->status = 'submitted';
            $submission->save();

            // Delete old files
            foreach ($submission->submissionFiles as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
        } else {
            // Create new submission
            $submission = Submissions::create([
                'assignment_id' => $assignmentId,
                'student_id' => $user->id,
                'file_path' => '', // Keep for backward compatibility
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);
        }

        // Store uploaded files
        foreach ($request->file('files') as $file) {
            $path = $file->store('submissions/' . $submission->id, 'public');
            
            SubmissionFile::create([
                'submission_id' => $submission->id,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()->route('assignment.submission', $assignmentId)
            ->with('success', 'Assignment submitted successfully!');
    }

    /**
     * Store a comment on a submission
     */
    public function storeComment(Request $request, $assignmentId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $assignment = Assignments::findOrFail($assignmentId);
        $user = Auth::user();

        // Get submission
        if ($user->role === 'student') {
            $submission = Submissions::where('assignment_id', $assignmentId)
                ->where('student_id', $user->id)
                ->first();
            
            if (!$submission) {
                return back()->withErrors(['comment' => 'Please submit your assignment first before adding comments.']);
            }
        } else {
            // For lecturers, get the first submission for this assignment
            // In a full implementation, you might want to add a student_id parameter
            $submission = Submissions::where('assignment_id', $assignmentId)
                ->first();
            
            if (!$submission) {
                return back()->withErrors(['comment' => 'No submission found to comment on.']);
            }
        }

        SubmissionComments::create([
            'submission_id' => $submission->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        return redirect()->route('assignment.submission', $assignmentId)
            ->with('success', 'Comment added successfully!');
    }
}

