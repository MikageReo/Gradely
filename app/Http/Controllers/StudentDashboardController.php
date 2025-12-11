<?php

namespace App\Http\Controllers;

use App\Models\Assignments;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard with pending assignments.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $courseIds = $user->studentCourses()->pluck('courses.id');

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

        return response(view('student.student_dashboard', [
            'pendingAssignments' => $pendingAssignments,
        ]))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}



