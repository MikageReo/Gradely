<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\Assignments;
use App\Models\Submissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class LecturerController extends Controller
{
    /**
     * Display lecturer dashboard with all courses
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        // Get courses through course_lecturer
        $courses = Courses::whereHas('courseLecturers', function($query) use ($user) {
            $query->where('lecturer_id', $user->id);
        })
        ->withCount(['assignments'])
        ->get();

        return view('lecturer.lecturer_dashboard', [
            'courses' => $courses,
        ]);
    }

    /**
     * Display all courses for the logged-in lecturer
     */
    public function courses()
    {
        $user = Auth::user();
        
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        // Get courses through course_lecturer
        $courses = Courses::whereHas('courseLecturers', function($query) use ($user) {
            $query->where('lecturer_id', $user->id);
        })
        ->withCount(['assignments'])
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

        // Verify course belongs to lecturer through course_lecturer
        $course = Courses::where('id', $courseId)
            ->whereHas('courseLecturers', function($query) use ($user) {
                $query->where('lecturer_id', $user->id);
            })
            ->with(['assignments', 'courseLecturers.students.student'])
            ->firstOrFail();

        $assignments = Assignments::where('course_id', $courseId)
            ->withCount('submissions')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate analytics - get students from all course_lecturer sections
        $totalStudents = 0;
        foreach ($course->courseLecturers as $courseLecturer) {
            $totalStudents += $courseLecturer->students->count();
        }
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

        // Verify course belongs to lecturer through course_lecturer
        $course = Courses::where('id', $courseId)
            ->whereHas('courseLecturers', function($query) use ($user) {
                $query->where('lecturer_id', $user->id);
            })
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
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $publicPath = public_path('assignments');
            
            // Create directory if it doesn't exist
            if (!File::exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true);
            }
            
            // Move file to public/assignments with original name
            $file->move($publicPath, $originalName);
            $assignment->attachment = 'assignments/' . $originalName;
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

        // Verify course belongs to lecturer through course_lecturer
        $course = Courses::where('id', $courseId)
            ->whereHas('courseLecturers', function($query) use ($user) {
                $query->where('lecturer_id', $user->id);
            })
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

        // Handle attachment update
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($assignment->attachment) {
                $oldFilePath = public_path($assignment->attachment);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }
            
            // Store new attachment with original name
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $publicPath = public_path('assignments');
            
            // Create directory if it doesn't exist
            if (!File::exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true);
            }
            
            // Move file to public/assignments with original name
            $file->move($publicPath, $originalName);
            $assignment->attachment = 'assignments/' . $originalName;
        }
        // If no new file is uploaded, keep the existing attachment (do nothing)

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

        // Verify course belongs to lecturer through course_lecturer
        $course = Courses::where('id', $courseId)
            ->whereHas('courseLecturers', function($query) use ($user) {
                $query->where('lecturer_id', $user->id);
            })
            ->firstOrFail();

        $assignment = Assignments::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->where('lecturer_id', $user->id)
            ->firstOrFail();

        // Delete attachment if exists
        if ($assignment->attachment) {
            $filePath = public_path($assignment->attachment);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
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

        // Verify course belongs to lecturer through course_lecturer
        $course = Courses::where('id', $courseId)
            ->whereHas('courseLecturers', function($query) use ($user) {
                $query->where('lecturer_id', $user->id);
            })
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

