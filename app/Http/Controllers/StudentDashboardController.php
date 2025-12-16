<?php

namespace App\Http\Controllers;

use App\Models\Assignments;
use App\Models\Courses;
use App\Models\CourseLecturer;
use App\Models\CourseStudent;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard with pending assignments.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'student') {
            abort(403, 'Unauthorized');
        }
        // Get course IDs through course_lecturer -> course_student
        $courseIds = Courses::whereHas('courseLecturers.students', function($query) use ($user) {
            $query->where('student_id', $user->id);
        })->pluck('id');

        if ($courseIds->isEmpty()) {
            $pendingAssignments = collect();
        } else {
            $pendingAssignments = Assignments::with('course')
                ->whereIn('course_id', $courseIds)
                ->whereDoesntHave('submissions', function ($query) use ($user) {
                    $query->where('student_id', $user->id);
                })
                ->orderBy('due_date')
                ->get()
                ->map(function ($assignment) {
                    $assignment->computed_status = ($assignment->due_date && $assignment->due_date->isPast())
                        ? 'Overdue'
                        : 'Pending';
                    return $assignment;
                });
        }

        // Get enrolled courses
        $courses = Courses::whereIn('id', $courseIds)
            ->withCount(['assignments'])
            ->get();

        return response(view('student.student_dashboard', [
            'pendingAssignments' => $pendingAssignments,
            'courses' => $courses,
        ]))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Display a specific course detail for student
     */
    public function showCourse($courseId, Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        // Verify student is enrolled in this course
        $courseLecturerIds = \App\Models\CourseStudent::where('student_id', $user->id)
            ->pluck('course_lecturer_id');
        $enrolledCourseIds = \App\Models\CourseLecturer::whereIn('id', $courseLecturerIds)
            ->pluck('course_id')
            ->unique();
        
        if (!$enrolledCourseIds->contains($courseId)) {
            abort(403, 'You are not enrolled in this course.');
        }
        
        $course = Courses::where('id', $courseId)
            ->with(['courseLecturers.lecturer', 'assignments' => function($query) {
                $query->where('visibility', 'published')
                    ->orderBy('due_date', 'desc');
            }])
            ->firstOrFail();

        // Get assignments with submission status
        $assignments = $course->assignments->map(function($assignment) use ($user) {
            $submission = $assignment->submissions()
                ->where('student_id', $user->id)
                ->first();
            
            $assignment->has_submission = $submission !== null;
            $assignment->submission_status = $submission ? $submission->status : null;
            $assignment->score = $submission ? $submission->score : null;
            
            return $assignment;
        });

        return view('student.course_detail', [
            'course' => $course,
            'assignments' => $assignments,
        ]);
    }
}



