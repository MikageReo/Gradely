<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Courses;
use App\Models\Assignments;
use App\Models\Submissions;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Get statistics
        $totalStudents = User::where('role', 'student')->count();
        $totalLecturers = User::where('role', 'lecturer')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalCourses = Courses::count();
        $totalAssignments = Assignments::count();
        $totalSubmissions = Submissions::count();
        $pendingGrading = Submissions::whereNull('score')->count();
        $completedGrading = Submissions::whereNotNull('score')->count();
        
        // Get total enrolled students (distinct students across all courses)
        $totalEnrolledStudents = CourseStudent::distinct('student_id')->count();
        
        // Get recent courses
        $recentCourses = Courses::withCount(['courseLecturers'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Calculate students per course
        foreach ($recentCourses as $course) {
            $totalStudentsInCourse = CourseStudent::whereHas('courseLecturer', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })->distinct('student_id')->count();
            $course->total_students = $totalStudentsInCourse;
        }

        return response(view('admin.admin_dashboard', [
            'totalStudents' => $totalStudents,
            'totalLecturers' => $totalLecturers,
            'totalAdmins' => $totalAdmins,
            'totalCourses' => $totalCourses,
            'totalAssignments' => $totalAssignments,
            'totalSubmissions' => $totalSubmissions,
            'pendingGrading' => $pendingGrading,
            'completedGrading' => $completedGrading,
            'totalEnrolledStudents' => $totalEnrolledStudents,
            'recentCourses' => $recentCourses,
        ]))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}

