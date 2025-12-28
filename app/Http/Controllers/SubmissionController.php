<?php

namespace App\Http\Controllers;

use App\Models\Assignments;
use App\Models\Submissions;
use App\Models\SubmissionFile;
use App\Models\SubmissionComments;
use App\Models\FeedbackFile;
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
     * Access control:
     * - Students: can only view their own submission for assignments in enrolled courses
     * - Lecturers: can view any student's submission for assignments they created
     * - Requires student_id parameter for lecturers to view specific student submission
     */
    public function show($assignmentId, Request $request)
    {
        $assignment = Assignments::with(['course', 'lecturer', 'assignmentFiles'])
            ->findOrFail($assignmentId);

        $user = Auth::user();

        // Check permissions
        if ($user->role === 'student') {
            // Verify student is enrolled in the course
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
            if (!$courseIds->contains($assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }
        } elseif ($user->role === 'lecturer') {
            // Verify lecturer is assigned to this assignment's course
            $isAssignedLecturer = CourseLecturer::where('course_id', $assignment->course_id)
                ->where('lecturer_id', $user->id)
                ->exists();

            if (!$isAssignedLecturer && $assignment->lecturer_id !== $user->id) {
                abort(403, 'You are not authorized to view this assignment.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role === 'student') {
            $submission = Submissions::with(['submissionFiles', 'feedbackFiles', 'submissionComments' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            }])
                ->where('assignment_id', $assignmentId)
                ->where('student_id', $user->id)
                ->first();
        } else {
            // For lecturers, require student_id parameter
            $studentId = $request->input('student_id');

            if (!$studentId) {
                // If no student_id provided, redirect to grading page
                return redirect()->route('lecturer.grading', [
                    'courseId' => $assignment->course_id,
                    'assignmentId' => $assignmentId
                ])->with('info', 'Please select a student to view their submission.');
            }

            // Verify the student is enrolled in the course
            $isEnrolled = CourseStudent::where('student_id', $studentId)
                ->whereHas('courseLecturer', function ($query) use ($assignment) {
                    $query->where('course_id', $assignment->course_id);
                })
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'This student is not enrolled in this course.');
            }

            $submission = Submissions::with(['submissionFiles', 'feedbackFiles', 'submissionComments' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            }, 'student'])
                ->where('assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->firstOrFail();
        }

        $unreadCount = 0;
        if ($submission) {
            // Count unread comments from other users
            $unreadCount = SubmissionComments::where('submission_id', $submission->id)
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();

            // Mark all comments from other users as read when viewing the submission
            SubmissionComments::where('submission_id', $submission->id)
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('student.assignment_submission', [
            'assignment' => $assignment,
            'submission' => $submission,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Store a new submission or update existing one
     */
    public function store(Request $request, $assignmentId)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:pdf,doc,docx,txt,rtf,odt|max:20480', // 20MB max per file
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

    public function storeComment(Request $request, $assignmentId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'student_id' => 'nullable|exists:users,id',
        ]);

        $assignment = Assignments::findOrFail($assignmentId);
        $user = Auth::user();

        // Verify access to assignment
        if ($user->role === 'student') {
            // Verify student is enrolled in the course
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
            if (!$courseIds->contains($assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }

            // Get student's own submission
            $submission = Submissions::where('assignment_id', $assignmentId)
                ->where('student_id', $user->id)
                ->first();

            if (!$submission) {
                return back()->withErrors(['comment' => 'Please submit your assignment first before adding comments.']);
            }
        } elseif ($user->role === 'lecturer') {
            // Verify lecturer is assigned to this assignment's course
            $isAssignedLecturer = CourseLecturer::where('course_id', $assignment->course_id)
                ->where('lecturer_id', $user->id)
                ->exists();

            if (!$isAssignedLecturer && $assignment->lecturer_id !== $user->id) {
                abort(403, 'You are not authorized to comment on this assignment.');
            }

            // For lecturers, require student_id parameter
            $studentId = $request->input('student_id');
            if (!$studentId) {
                return back()->withErrors(['comment' => 'Student ID is required for lecturer comments.']);
            }

            // Verify the student is enrolled in the course
            $isEnrolled = CourseStudent::where('student_id', $studentId)
                ->whereHas('courseLecturer', function ($query) use ($assignment) {
                    $query->where('course_id', $assignment->course_id);
                })
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'This student is not enrolled in this course.');
            }

            $submission = Submissions::where('assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->first();

            if (!$submission) {
                return back()->withErrors(['comment' => 'No submission found for this student.']);
            }
        } else {
            abort(403, 'Unauthorized access.');
        }

        $comment = SubmissionComments::create([
            'submission_id' => $submission->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        // Load the user relationship for the response
        $comment->load('user');

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $user->role === 'lecturer'
                    ? 'Your reply was sent to the student.'
                    : 'Your question was sent to the lecturer.',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user_id' => $comment->user_id,
                    'user_name' => $comment->user->name,
                    'user_role' => $comment->user->role,
                    'created_at' => $comment->created_at->toISOString(),
                ]
            ]);
        }

        // Redirect with student_id for lecturers (as query parameter)
        $redirectUrl = route('assignment.submission', ['assignmentId' => $assignmentId]);
        if ($user->role === 'lecturer' && $request->has('student_id')) {
            $redirectUrl .= '?student_id=' . $request->input('student_id');
        }

        return redirect($redirectUrl)
            ->with('success', $user->role === 'lecturer'
                ? 'Your reply was sent to the student.'
                : 'Your question was sent to the lecturer.')
            ->with('success_type', 'comment');
    }

    /**
     * Update submission grade and feedback
     * Access control:
     * - Only lecturers assigned to the course can grade
     * - Verify submission belongs to the assignment and student is enrolled
     */
    public function updateGrade(Request $request, $assignmentId)
    {
        $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'score' => 'nullable|numeric|min:0|max:100',
            'lecturer_feedback' => 'nullable|string|max:2000',
            'feedback_files' => 'nullable|array',
            'feedback_files.*' => 'file|mimes:pdf,doc,docx,txt,rtf,odt,jpg,jpeg,png,gif|max:20480', // 20MB max per file
        ]);

        $assignment = Assignments::findOrFail($assignmentId);
        $user = Auth::user();

        // Only lecturers can grade
        if ($user->role !== 'lecturer') {
            abort(403, 'Only lecturers can grade submissions.');
        }

        // Verify lecturer is assigned to this assignment's course
        $isAssignedLecturer = CourseLecturer::where('course_id', $assignment->course_id)
            ->where('lecturer_id', $user->id)
            ->exists();

        if (!$isAssignedLecturer && $assignment->lecturer_id !== $user->id) {
            abort(403, 'You are not authorized to grade this assignment.');
        }

        // Get submission and verify it belongs to this assignment
        $submission = Submissions::where('id', $request->submission_id)
            ->where('assignment_id', $assignmentId)
            ->firstOrFail();

        // Verify the student is enrolled in the course
        $isEnrolled = CourseStudent::where('student_id', $submission->student_id)
            ->whereHas('courseLecturer', function ($query) use ($assignment) {
                $query->where('course_id', $assignment->course_id);
            })
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'This student is not enrolled in this course.');
        }

        $submission->score = $request->score;
        $submission->grade = $this->calculateGrade($request->score);
        $submission->lecturer_feedback = $request->lecturer_feedback;
        $submission->status = 'marked';
        $submission->marked_at = now();
        $submission->save();

        // Handle feedback file uploads
        if ($request->hasFile('feedback_files')) {
            $publicPath = public_path('feedback/' . $submission->id);
            if (!File::exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true);
            }

            foreach ($request->file('feedback_files') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileType = $file->getClientMimeType();

                // Handle duplicate filenames by adding timestamp
                $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $uniqueName = $fileName . '_' . time() . '_' . uniqid() . '.' . $extension;

                // Move file to public folder
                $file->move($publicPath, $uniqueName);
                $relativePath = 'feedback/' . $submission->id . '/' . $uniqueName;

                FeedbackFile::create([
                    'submission_id' => $submission->id,
                    'file_path' => $relativePath,
                    'original_filename' => $originalName,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                ]);
            }
        }

        // Reload feedback files relationship
        $submission->load('feedbackFiles');

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'The grade and feedback have been saved for this submission.',
                'submission' => [
                    'score' => $submission->score,
                    'grade' => $submission->grade,
                    'lecturer_feedback' => $submission->lecturer_feedback,
                    'status' => $submission->status,
                    'marked_at' => $submission->marked_at ? $submission->marked_at->toISOString() : null,
                    'feedback_files' => $submission->feedbackFiles->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'original_filename' => $file->original_filename,
                            'file_size' => $file->file_size,
                        ];
                    }),
                ]
            ]);
        }

        // Redirect with student_id for lecturers (as query parameter)
        $studentId = $request->has('student_id') ? $request->input('student_id') : $submission->student_id;
        $redirectUrl = route('assignment.submission', ['assignmentId' => $assignmentId]) . '?student_id=' . $studentId;

        return redirect($redirectUrl)
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

    /**
     * Download assignment attachment (protected route)
     * Access control: Only enrolled students and assigned lecturers can download
     */
    public function downloadAssignmentAttachment($assignmentId)
    {
        $assignment = Assignments::findOrFail($assignmentId);
        $user = Auth::user();

        if (!$assignment->attachment) {
            abort(404, 'Assignment attachment not found.');
        }

        // Check permissions
        if ($user->role === 'student') {
            // Verify student is enrolled in the course
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
            if (!$courseIds->contains($assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }
        } elseif ($user->role === 'lecturer') {
            // Verify lecturer is assigned to this assignment's course
            $isAssignedLecturer = CourseLecturer::where('course_id', $assignment->course_id)
                ->where('lecturer_id', $user->id)
                ->exists();

            if (!$isAssignedLecturer && $assignment->lecturer_id !== $user->id) {
                abort(403, 'You are not authorized to access this assignment.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }

        $filePath = public_path($assignment->attachment);
        if (!File::exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, basename($assignment->attachment));
    }

    /**
     * Download submission file (protected route)
     * Access control:
     * - Students can only download their own submission files
     * - Lecturers can download files for students enrolled in their courses
     */
    public function downloadSubmissionFile($submissionId, $fileId)
    {
        $user = Auth::user();

        $submission = Submissions::with(['assignment', 'student'])->findOrFail($submissionId);
        $file = SubmissionFile::where('id', $fileId)
            ->where('submission_id', $submissionId)
            ->firstOrFail();

        // Check permissions
        if ($user->role === 'student') {
            // Students can only download their own files
            if ($submission->student_id !== $user->id) {
                abort(403, 'You can only download your own submission files.');
            }

            // Verify student is enrolled in the course
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
            if (!$courseIds->contains($submission->assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }
        } elseif ($user->role === 'lecturer') {
            // Verify lecturer is assigned to this assignment's course
            $isAssignedLecturer = CourseLecturer::where('course_id', $submission->assignment->course_id)
                ->where('lecturer_id', $user->id)
                ->exists();

            if (!$isAssignedLecturer && $submission->assignment->lecturer_id !== $user->id) {
                abort(403, 'You are not authorized to access this submission.');
            }

            // Verify the student is enrolled in the course
            $isEnrolled = CourseStudent::where('student_id', $submission->student_id)
                ->whereHas('courseLecturer', function ($query) use ($submission) {
                    $query->where('course_id', $submission->assignment->course_id);
                })
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'This student is not enrolled in this course.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }

        $filePath = public_path($file->file_path);
        if (!File::exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $file->original_filename);
    }

    /**
     * Delete a submission file
     * Access control:
     * - Students can only delete their own files
     * - Only allowed if submission is not yet graded
     */
    public function deleteSubmissionFile($submissionId, $fileId)
    {
        $user = Auth::user();

        $submission = Submissions::with(['assignment', 'student'])->findOrFail($submissionId);
        $file = SubmissionFile::where('id', $fileId)
            ->where('submission_id', $submissionId)
            ->firstOrFail();

        // Check permissions
        if ($user->role === 'student') {
            // Students can only delete their own files
            if ($submission->student_id !== $user->id) {
                abort(403, 'You can only delete your own submission files.');
            }

            // Verify student is enrolled in the course
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
            if (!$courseIds->contains($submission->assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }

            // Students can only delete files if submission is not graded
            if ($submission->status === 'marked' || $submission->score !== null) {
                abort(403, 'You cannot delete files from a graded submission.');
            }
        } elseif ($user->role === 'lecturer') {
            // Lecturers can delete files for students enrolled in their courses
            $isAssignedLecturer = CourseLecturer::where('course_id', $submission->assignment->course_id)
                ->where('lecturer_id', $user->id)
                ->exists();

            if (!$isAssignedLecturer && $submission->assignment->lecturer_id !== $user->id) {
                abort(403, 'You are not authorized to delete this file.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }

        // Delete physical file
        $filePath = public_path($file->file_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete file record
        $file->delete();

        // If no files remain and submission exists, update submission status
        $remainingFiles = SubmissionFile::where('submission_id', $submissionId)->count();
        if ($remainingFiles === 0 && $submission->status === 'submitted') {
            $submission->status = 'draft';
            $submission->save();
        }

        // If AJAX request, return JSON response
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
                'remaining_files' => $remainingFiles
            ]);
        }

        return redirect()->route('assignment.submission', ['assignmentId' => $submission->assignment_id])
            ->with('success', 'File deleted successfully.');
    }

    /**
     * Replace/update a submission file
     * Access control:
     * - Students can only replace their own files
     * - Only allowed if submission is not yet graded
     */
    public function replaceSubmissionFile(Request $request, $submissionId, $fileId)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt,rtf,odt|max:20480', // 20MB max
        ]);

        $user = Auth::user();

        $submission = Submissions::with(['assignment', 'student'])->findOrFail($submissionId);
        $file = SubmissionFile::where('id', $fileId)
            ->where('submission_id', $submissionId)
            ->firstOrFail();

        // Check permissions
        if ($user->role === 'student') {
            // Students can only replace their own files
            if ($submission->student_id !== $user->id) {
                abort(403, 'You can only replace your own submission files.');
            }

            // Verify student is enrolled in the course
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
            if (!$courseIds->contains($submission->assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }

            // Students can only replace files if submission is not graded
            if ($submission->status === 'marked' || $submission->score !== null) {
                abort(403, 'You cannot replace files in a graded submission.');
            }
        } elseif ($user->role === 'lecturer') {
            // Lecturers can replace files for students enrolled in their courses
            $isAssignedLecturer = CourseLecturer::where('course_id', $submission->assignment->course_id)
                ->where('lecturer_id', $user->id)
                ->exists();

            if (!$isAssignedLecturer && $submission->assignment->lecturer_id !== $user->id) {
                abort(403, 'You are not authorized to replace this file.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }

        // Delete old physical file
        $oldFilePath = public_path($file->file_path);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }

        // Upload new file
        $newFile = $request->file('file');
        $originalName = $newFile->getClientOriginalName();
        $fileSize = $newFile->getSize();
        $fileType = $newFile->getMimeType();

        // Handle duplicate filenames
        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueName = $fileName . '_' . time() . '_' . uniqid() . '.' . $extension;

        $publicPath = public_path('submissions/' . $submissionId);
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        // Move new file
        $newFile->move($publicPath, $uniqueName);
        $relativePath = 'submissions/' . $submissionId . '/' . $uniqueName;

        // Update file record
        $file->file_path = $relativePath;
        $file->original_filename = $originalName;
        $file->file_type = $fileType;
        $file->file_size = $fileSize;
        $file->save();

        // Update submission timestamp
        $submission->submitted_at = now();
        $submission->save();

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'File replaced successfully.',
                'file' => [
                    'id' => $file->id,
                    'original_filename' => $file->original_filename,
                    'file_size' => $file->file_size,
                ]
            ]);
        }

        return redirect()->route('assignment.submission', ['assignmentId' => $submission->assignment_id])
            ->with('success', 'File replaced successfully.');
    }

    /**
     * Download feedback file (protected route)
     * Access control:
     * - Students can download feedback files for their own submissions
     * - Lecturers can download feedback files for submissions they can grade
     */
    public function downloadFeedbackFile($submissionId, $fileId)
    {
        $user = Auth::user();

        $submission = Submissions::with(['assignment', 'student'])->findOrFail($submissionId);
        $file = FeedbackFile::where('id', $fileId)
            ->where('submission_id', $submissionId)
            ->firstOrFail();

        // Check permissions
        if ($user->role === 'student') {
            // Students can only download feedback files for their own submissions
            if ($submission->student_id !== $user->id) {
                abort(403, 'You can only download feedback files for your own submissions.');
            }

            // Verify student is enrolled in the course
            $courseLecturerIds = CourseStudent::where('student_id', $user->id)
                ->pluck('course_lecturer_id');
            $courseIds = CourseLecturer::whereIn('id', $courseLecturerIds)
                ->pluck('course_id')
                ->unique();
            if (!$courseIds->contains($submission->assignment->course_id)) {
                abort(403, 'You are not enrolled in this course.');
            }
        } elseif ($user->role === 'lecturer') {
            // Verify lecturer is assigned to this assignment's course
            $isAssignedLecturer = CourseLecturer::where('course_id', $submission->assignment->course_id)
                ->where('lecturer_id', $user->id)
                ->exists();

            if (!$isAssignedLecturer && $submission->assignment->lecturer_id !== $user->id) {
                abort(403, 'You are not authorized to access this feedback file.');
            }

            // Verify the student is enrolled in the course
            $isEnrolled = CourseStudent::where('student_id', $submission->student_id)
                ->whereHas('courseLecturer', function ($query) use ($submission) {
                    $query->where('course_id', $submission->assignment->course_id);
                })
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'This student is not enrolled in this course.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }

        $filePath = public_path($file->file_path);
        if (!File::exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $file->original_filename);
    }

    /**
     * Delete a feedback file
     * Access control:
     * - Only lecturers can delete feedback files
     */
    public function deleteFeedbackFile($submissionId, $fileId)
    {
        $user = Auth::user();

        if ($user->role !== 'lecturer') {
            abort(403, 'Only lecturers can delete feedback files.');
        }

        $submission = Submissions::with(['assignment', 'student'])->findOrFail($submissionId);
        $file = FeedbackFile::where('id', $fileId)
            ->where('submission_id', $submissionId)
            ->firstOrFail();

        // Verify lecturer is assigned to this assignment's course
        $isAssignedLecturer = CourseLecturer::where('course_id', $submission->assignment->course_id)
            ->where('lecturer_id', $user->id)
            ->exists();

        if (!$isAssignedLecturer && $submission->assignment->lecturer_id !== $user->id) {
            abort(403, 'You are not authorized to delete this feedback file.');
        }

        // Delete physical file
        $filePath = public_path($file->file_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete file record
        $file->delete();

        // If AJAX request, return JSON response
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Feedback file deleted successfully.',
            ]);
        }

        return redirect()->route('assignment.submission', ['assignmentId' => $submission->assignment_id, 'student_id' => $submission->student_id])
            ->with('success', 'Feedback file deleted successfully.');
    }
}
