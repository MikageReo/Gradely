<?php

namespace App\Http\Controllers;

use App\Models\Assignments;
use App\Models\Courses;
use App\Models\CourseLecturer;
use App\Models\CourseStudent;
use App\Models\Submissions;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard with pending assignments.
     */
    public function index(Request $request)
    {
        $user = $request->user();
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

        // Get enrolled courses with performance metrics
        $courses = Courses::whereIn('id', $courseIds)
            ->withCount(['assignments'])
            ->get()
            ->map(function ($course) use ($user) {
                $course->performance = $this->calculateCoursePerformance($course, $user->id);
                return $course;
            });

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
            $assignment->grade = $submission ? $submission->grade : null;
            
            return $assignment;
        });

        // Calculate course performance
        $performance = $this->calculateCoursePerformance($course, $user->id);

        return view('student.course_detail', [
            'course' => $course,
            'assignments' => $assignments,
            'performance' => $performance,
        ]);
    }

    /**
     * Calculate performance metrics for a course
     * 
     * @param Courses $course
     * @param int $studentId
     * @return array
     */
    private function calculateCoursePerformance($course, $studentId)
    {
        // Get all assignments for this course
        $assignments = $course->assignments()->where('visibility', 'published')->get();
        
        // Get all submissions for this student in this course
        $assignmentIds = $assignments->pluck('id');
        $submissions = Submissions::whereIn('assignment_id', $assignmentIds)
            ->where('student_id', $studentId)
            ->get();

        // Calculate metrics
        $totalAssignments = $assignments->count();
        $submittedCount = $submissions->where('status', 'submitted')->count() + 
                         $submissions->where('status', 'marked')->count();
        $gradedCount = $submissions->where('status', 'marked')->count();
        
        // Calculate average score from graded submissions
        $gradedSubmissions = $submissions->where('status', 'marked')->whereNotNull('score');
        $averageScore = $gradedSubmissions->count() > 0 
            ? round($gradedSubmissions->avg('score'), 2) 
            : null;
        
        // Calculate average letter grade
        $averageGrade = null;
        if ($averageScore !== null) {
            $averageGrade = $this->calculateGrade($averageScore);
        }

        // Calculate completion percentage
        $completionPercentage = $totalAssignments > 0 
            ? round(($submittedCount / $totalAssignments) * 100) 
            : 0;

        // Determine performance level and color
        $performanceLevel = $this->getPerformanceLevel($averageScore);
        
        return [
            'total_assignments' => $totalAssignments,
            'submitted_count' => $submittedCount,
            'graded_count' => $gradedCount,
            'average_score' => $averageScore,
            'average_grade' => $averageGrade,
            'completion_percentage' => $completionPercentage,
            'performance_level' => $performanceLevel,
            'has_grades' => $gradedCount > 0,
        ];
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
     * Get performance level and color based on average score
     * 
     * @param float|null $averageScore
     * @return array
     */
    private function getPerformanceLevel($averageScore)
    {
        if ($averageScore === null) {
            return [
                'level' => 'No grades yet',
                'color' => '#9E9E9E',
                'bg_color' => '#F5F5F5',
                'text_color' => '#757575',
            ];
        }

        if ($averageScore >= 80) {
            return [
                'level' => 'Excellent',
                'color' => '#4CAF50',
                'bg_color' => '#E8F5E9',
                'text_color' => '#2E7D32',
            ];
        } elseif ($averageScore >= 70) {
            return [
                'level' => 'Good',
                'color' => '#2196F3',
                'bg_color' => '#E3F2FD',
                'text_color' => '#1565C0',
            ];
        } elseif ($averageScore >= 60) {
            return [
                'level' => 'Average',
                'color' => '#FF9800',
                'bg_color' => '#FFF3E0',
                'text_color' => '#E65100',
            ];
        } elseif ($averageScore >= 50) {
            return [
                'level' => 'Needs Improvement',
                'color' => '#FF5722',
                'bg_color' => '#FFEBEE',
                'text_color' => '#C62828',
            ];
        } else {
            return [
                'level' => 'Critical',
                'color' => '#F44336',
                'bg_color' => '#FFEBEE',
                'text_color' => '#C62828',
            ];
        }
    }
}




