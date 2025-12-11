<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\Assignments;
use App\Models\Submissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LecturerController extends Controller
{
    /**
     * Display all courses for the logged-in lecturer
     */
    public function courses()
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        $courses = Courses::where('lecturer_id', $user->id)
            ->withCount(['assignments', 'students'])
            ->get();

        return view('lecturer.courses', [
            'courses' => $courses,
        ]);
    }

    /**
     * Display a specific course with its assignments
     */
    public function showCourse($courseId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        $course = Courses::where('id', $courseId)
            ->where('lecturer_id', $user->id)
            ->with(['assignments', 'students'])
            ->firstOrFail();

        $assignments = Assignments::where('course_id', $courseId)
            ->withCount('submissions')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate analytics
        $totalStudents = $course->students->count();
        $totalSubmissions = Submissions::whereIn('assignment_id', $assignments->pluck('id'))->count();
        $pendingGrading = Submissions::whereIn('assignment_id', $assignments->pluck('id'))
            ->whereNull('score')
            ->count();
        $completed = Submissions::whereIn('assignment_id', $assignments->pluck('id'))
            ->whereNotNull('score')
            ->count();

        return view('lecturer.course_detail', [
            'course' => $course,
            'assignments' => $assignments,
            'totalStudents' => $totalStudents,
            'totalSubmissions' => $totalSubmissions,
            'pendingGrading' => $pendingGrading,
            'completed' => $completed,
        ]);
    }

    /**
     * Store a new assignment
     */
    public function storeAssignment(Request $request, $courseId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        // Verify course belongs to lecturer
        $course = Courses::where('id', $courseId)
            ->where('lecturer_id', $user->id)
            ->firstOrFail();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:open,close',
            'visibility' => 'required|in:published,hidden',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $assignment = new Assignments();
        $assignment->course_id = $courseId;
        $assignment->lecturer_id = $user->id;
        $assignment->title = $data['title'];
        $assignment->description = $data['description'] ?? null;
        $assignment->due_date = $data['due_date'] ?? null;
        $assignment->status = $data['status'];
        $assignment->visibility = $data['visibility'];

        if ($request->hasFile('attachment')) {
            $assignment->attachment = $request->file('attachment')->store('assignments', 'public');
        }

        $assignment->save();

        return redirect()->route('lecturer.course.show', $courseId)
            ->with('success', 'Assignment created successfully!');
    }

    /**
     * Update an assignment
     */
    public function updateAssignment(Request $request, $courseId, $assignmentId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        // Verify course belongs to lecturer
        $course = Courses::where('id', $courseId)
            ->where('lecturer_id', $user->id)
            ->firstOrFail();

        $assignment = Assignments::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->where('lecturer_id', $user->id)
            ->firstOrFail();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:open,close',
            'visibility' => 'required|in:published,hidden',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $assignment->title = $data['title'];
        $assignment->description = $data['description'] ?? null;
        $assignment->due_date = $data['due_date'] ?? null;
        $assignment->status = $data['status'];
        $assignment->visibility = $data['visibility'];

        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($assignment->attachment) {
                Storage::disk('public')->delete($assignment->attachment);
            }
            $assignment->attachment = $request->file('attachment')->store('assignments', 'public');
        }

        $assignment->save();

        return redirect()->route('lecturer.course.show', $courseId)
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Delete an assignment
     */
    public function deleteAssignment($courseId, $assignmentId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        // Verify course belongs to lecturer
        $course = Courses::where('id', $courseId)
            ->where('lecturer_id', $user->id)
            ->firstOrFail();

        $assignment = Assignments::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->where('lecturer_id', $user->id)
            ->firstOrFail();

        // Delete attachment if exists
        if ($assignment->attachment) {
            Storage::disk('public')->delete($assignment->attachment);
        }

        $assignment->delete();

        return redirect()->route('lecturer.course.show', $courseId)
            ->with('success', 'Assignment deleted successfully!');
    }

    /**
     * View all submissions for grading
     */
    public function viewGrading($courseId, $assignmentId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        // Verify course belongs to lecturer
        $course = Courses::where('id', $courseId)
            ->where('lecturer_id', $user->id)
            ->firstOrFail();

        $assignment = Assignments::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->where('lecturer_id', $user->id)
            ->with('course')
            ->firstOrFail();

        $submissions = Submissions::where('assignment_id', $assignmentId)
            ->with(['student', 'submissionFiles'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('lecturer.grading', [
            'course' => $course,
            'assignment' => $assignment,
            'submissions' => $submissions,
        ]);
    }
}

