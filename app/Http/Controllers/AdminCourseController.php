<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\CourseLecturer;
use App\Models\CourseStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CourseEnrolledMail;

class AdminCourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $courses = Courses::withCount(['courseLecturers'])
            ->with(['courseLecturers.lecturer', 'courseLecturers.students'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate total students per course
        foreach ($courses as $course) {
            $totalStudents = 0;
            foreach ($course->courseLecturers as $cl) {
                $totalStudents += $cl->students->count();
            }
            $course->total_students = $totalStudents;
        }

        return view('admin.courses.index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('admin.courses.create');
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'course_code' => 'required|string|max:50|unique:courses,course_code',
            'course_name' => 'required|string|max:255',
        ]);

        $course = Courses::create([
            'course_code' => $data['course_code'],
            'course_name' => $data['course_name'],
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified course with its lecturers and students
     */
    public function show($courseId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $course = Courses::with([
            'courseLecturers.lecturer',
            'courseLecturers.students.student',
            'assignments'
        ])
        ->findOrFail($courseId);

        // Get all lecturers for assignment dropdown
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        
        // Get all students for enrollment
        $allStudents = User::where('role', 'student')->orderBy('name')->get();

        return view('admin.courses.show', [
            'course' => $course,
            'lecturers' => $lecturers,
            'allStudents' => $allStudents,
        ]);
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit($courseId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $course = Courses::findOrFail($courseId);

        return view('admin.courses.edit', [
            'course' => $course,
        ]);
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, $courseId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $course = Courses::findOrFail($courseId);

        $data = $request->validate([
            'course_code' => 'required|string|max:50|unique:courses,course_code,' . $courseId,
            'course_name' => 'required|string|max:255',
        ]);

        $course->update([
            'course_code' => $data['course_code'],
            'course_name' => $data['course_name'],
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course
     */
    public function destroy($courseId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $course = Courses::findOrFail($courseId);
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully!');
    }

    /**
     * Assign a lecturer to a course
     */
    public function assignLecturer(Request $request, $courseId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'lecturer_id' => 'required|exists:users,id',
            'section' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:0',
        ]);

        // Check if this combination already exists
        $exists = CourseLecturer::where('course_id', $courseId)
            ->where('lecturer_id', $data['lecturer_id'])
            ->where('section', $data['section'] ?? null)
            ->exists();

        if ($exists) {
            return back()->withErrors(['lecturer_id' => 'This lecturer is already assigned to this course section.']);
        }

        CourseLecturer::create([
            'course_id' => $courseId,
            'lecturer_id' => $data['lecturer_id'],
            'section' => $data['section'] ?? null,
            'capacity' => $data['capacity'] ?? 0,
        ]);

        return back()->with('success', 'Lecturer assigned successfully!');
    }

    /**
     * Remove a lecturer assignment from a course
     */
    public function removeLecturer($courseId, $courseLecturerId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $courseLecturer = CourseLecturer::where('course_id', $courseId)
            ->where('id', $courseLecturerId)
            ->firstOrFail();

        $courseLecturer->delete();

        return back()->with('success', 'Lecturer assignment removed successfully!');
    }

    /**
     * Enroll a student in a course section (supports single or multiple students)
     */
    public function enrollStudent(Request $request, $courseId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
            'course_lecturer_id' => 'required|exists:course_lecturer,id',
        ]);

        // Verify course_lecturer belongs to this course
        $courseLecturer = CourseLecturer::with('lecturer')
            ->where('id', $data['course_lecturer_id'])
            ->where('course_id', $courseId)
            ->firstOrFail();

        $enrolled = 0;
        $skipped = 0;
        $errors = [];

        foreach ($data['student_ids'] as $studentId) {
            // Verify user is a student
            $student = User::where('id', $studentId)->where('role', 'student')->first();
            if (!$student) {
                $skipped++;
                continue;
            }

            // Check if student is already enrolled in this section
            $exists = CourseStudent::where('student_id', $studentId)
                ->where('course_lecturer_id', $data['course_lecturer_id'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Check capacity
            $currentEnrollment = CourseStudent::where('course_lecturer_id', $data['course_lecturer_id'])->count();
            if ($courseLecturer->capacity > 0 && $currentEnrollment >= $courseLecturer->capacity) {
                $errors[] = "Capacity reached. Cannot enroll more students.";
                break;
            }

            CourseStudent::create([
                'student_id' => $studentId,
                'course_lecturer_id' => $data['course_lecturer_id'],
            ]);

            // Send enrollment email
            try {
                $course = Courses::find($courseId);
                Mail::to($student->email)->send(new CourseEnrolledMail($student, $course, $courseLecturer));
            } catch (\Exception $e) {
                // Log error but don't fail the enrollment
                Log::error('Failed to send enrollment email to ' . $student->email . ': ' . $e->getMessage());
            }

            $enrolled++;
        }

        $message = "Successfully enrolled {$enrolled} student(s).";
        if ($skipped > 0) {
            $message .= " {$skipped} student(s) were skipped (already enrolled or invalid).";
        }
        if (!empty($errors)) {
            return back()->withErrors(['enrollment' => implode(' ', $errors)])->with('partial_success', $message);
        }

        return back()->with('success', $message);
    }

    /**
     * Bulk enroll students from CSV file
     */
    public function bulkEnrollStudent(Request $request, $courseId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'course_lecturer_id' => 'required|exists:course_lecturer,id',
        ]);

        $courseLecturer = CourseLecturer::with('lecturer')
            ->where('id', $request->course_lecturer_id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $file = $request->file('csv_file');
        $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $enrolled = 0;
        $skipped = 0;
        $notFound = [];

        foreach ($lines as $line) {
            // Handle comma-separated values
            $emails = array_map('trim', explode(',', $line));
            
            foreach ($emails as $email) {
                if (empty($email)) continue;

                $student = User::where('email', $email)->where('role', 'student')->first();
                
                if (!$student) {
                    $notFound[] = $email;
                    continue;
                }

                // Check if already enrolled
                $exists = CourseStudent::where('student_id', $student->id)
                    ->where('course_lecturer_id', $courseLecturer->id)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Check capacity
                $currentEnrollment = CourseStudent::where('course_lecturer_id', $courseLecturer->id)->count();
                if ($courseLecturer->capacity > 0 && $currentEnrollment >= $courseLecturer->capacity) {
                    break;
                }

                CourseStudent::create([
                    'student_id' => $student->id,
                    'course_lecturer_id' => $courseLecturer->id,
                ]);

                // Send enrollment email
                try {
                    $course = Courses::find($courseId);
                    Mail::to($student->email)->send(new CourseEnrolledMail($student, $course, $courseLecturer));
                } catch (\Exception $e) {
                    // Log error but don't fail the enrollment
                    Log::error('Failed to send enrollment email to ' . $student->email . ': ' . $e->getMessage());
                }

                $enrolled++;
            }
        }

        $message = "Successfully enrolled {$enrolled} student(s).";
        if ($skipped > 0) {
            $message .= " {$skipped} already enrolled.";
        }
        if (count($notFound) > 0) {
            $notFoundList = count($notFound) > 5 ? implode(', ', array_slice($notFound, 0, 5)) . ' and ' . (count($notFound) - 5) . ' more' : implode(', ', $notFound);
            $message .= " " . count($notFound) . " email(s) not found: " . $notFoundList;
        }

        return back()->with('success', $message);
    }

    /**
     * Remove a student enrollment
     */
    public function removeStudent($courseId, $enrollmentId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $enrollment = CourseStudent::findOrFail($enrollmentId);
        
        // Verify it belongs to a course_lecturer of this course
        $courseLecturer = CourseLecturer::where('id', $enrollment->course_lecturer_id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $enrollment->delete();

        return back()->with('success', 'Student enrollment removed successfully!');
    }

    /**
     * Download CSV template for bulk student enrollment
     */
    public function downloadEnrollmentTemplate()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $filename = 'student_enrollment_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add header row only
            fputcsv($file, ['Email']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
