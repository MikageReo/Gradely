<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Courses;
use App\Models\Assignments;
use App\Models\Submissions;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $totalEnrolledStudents = DB::table('course_student')
            ->select(DB::raw('COUNT(DISTINCT student_id) as count'))
            ->value('count') ?? 0;
        
        // Get recent courses
        $recentCourses = Courses::withCount(['courseLecturers'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Calculate students per course
        foreach ($recentCourses as $course) {
            // Get all course_lecturer_ids for this course
            $courseLecturerIds = DB::table('course_lecturer')
                ->where('course_id', $course->id)
                ->pluck('id');
            
            // Count distinct students enrolled in any section of this course
            if ($courseLecturerIds->isNotEmpty()) {
                $totalStudentsInCourse = DB::table('course_student')
                    ->whereIn('course_lecturer_id', $courseLecturerIds)
                    ->select(DB::raw('COUNT(DISTINCT student_id) as count'))
                    ->value('count') ?? 0;
            } else {
                $totalStudentsInCourse = 0;
            }
            
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

