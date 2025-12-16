<?php

namespace App\Http\Controllers;

use App\Models\Assignments;
use App\Models\Submissions;
use App\Models\SubmissionFile;
use App\Models\SubmissionComments;
use App\Models\CourseLecturer;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
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
            $submission = Submissions::with(['submissionFiles', 'submissionComments' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            }])
                ->where('assignment_id', $assignmentId)
                ->where('student_id', $user->id)
                ->first();
        } else {
            // For lecturers, get the first submission for this assignment
            // In a full implementation, you might want to add a student_id parameter
            $submission = Submissions::with(['submissionFiles', 'submissionComments' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            }, 'student'])
                ->where('assignment_id', $assignmentId)
                ->first();
        }

        return view('student.assignment_submission', [
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
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
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
                $filePath = public_path($file->file_path);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
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

        // Store uploaded files in public folder
        $publicPath = public_path('submissions/' . $submission->id);
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            // Get file info BEFORE moving the file
            $fileSize = $file->getSize();
            $fileType = $file->getClientMimeType();

            // Handle duplicate filenames by adding timestamp
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueName = $fileName . '_' . time() . '_' . uniqid() . '.' . $extension;

            // Move file to public folder
            $file->move($publicPath, $uniqueName);
            $relativePath = 'submissions/' . $submission->id . '/' . $uniqueName;

            SubmissionFile::create([
                'submission_id' => $submission->id,
                'file_path' => $relativePath,
                'original_filename' => $originalName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
            ]);
        }

        return redirect()->route('assignment.submission', $assignmentId)
            ->with('success', 'Your assignment files were uploaded and submitted to your lecturer.')
            ->with('success_type', 'submission');
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
            ->with('success', $user->role === 'lecturer'
                ? 'Your reply was sent to the student.'
                : 'Your question was sent to the lecturer.')
            ->with('success_type', 'comment');
    }

    /**
     * Update submission grade and feedback
     */
    public function updateGrade(Request $request, $assignmentId)
    {
        $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'score' => 'nullable|numeric|min:0|max:100',
            'lecturer_feedback' => 'nullable|string|max:2000',
        ]);

        $assignment = Assignments::findOrFail($assignmentId);
        $user = Auth::user();

        // Only lecturers can grade
        if ($user->role !== 'lecturer') {
            abort(403, 'Only lecturers can grade submissions.');
        }

        // Verify lecturer owns this assignment
        if ($assignment->lecturer_id !== $user->id) {
            abort(403, 'You are not authorized to grade this assignment.');
        }

        $submission = Submissions::where('id', $request->submission_id)
            ->where('assignment_id', $assignmentId)
            ->firstOrFail();

        $submission->score = $request->score;
        $submission->grade = $this->calculateGrade($request->score);
        $submission->lecturer_feedback = $request->lecturer_feedback;
        $submission->status = 'marked';
        $submission->marked_at = now();
        $submission->save();

        return redirect()->route('assignment.submission', $assignmentId)
            ->with('success', 'The grade and feedback have been saved for this submission.')
            ->with('success_type', 'grade');
    }

    /**
     * Calculate grade letter based on score
     *
     * @param float|null $score
     * @return string|null
     */
    private function calculateGrade($score)
    {
        if ($score === null) {
            return null;
        }

        if ($score >= 80 && $score <= 100) {
            return 'A';
        } elseif ($score >= 70 && $score <= 79) {
            return 'B';
        } elseif ($score >= 60 && $score <= 69) {
            return 'C';
        } elseif ($score >= 50 && $score <= 59) {
            return 'D';
        } else {
            return 'F';
        }
    }
}
