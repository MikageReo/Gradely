<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\CourseLecturer;
use App\Models\CourseStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Enroll a student in a course section
     */
    public function enrollStudent(Request $request, $courseId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_lecturer_id' => 'required|exists:course_lecturer,id',
        ]);

        // Verify course_lecturer belongs to this course
        $courseLecturer = CourseLecturer::where('id', $data['course_lecturer_id'])
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Check if student is already enrolled in this section
        $exists = CourseStudent::where('student_id', $data['student_id'])
            ->where('course_lecturer_id', $data['course_lecturer_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['student_id' => 'Student is already enrolled in this course section.']);
        }

        // Check capacity
        $currentEnrollment = CourseStudent::where('course_lecturer_id', $data['course_lecturer_id'])->count();
        if ($courseLecturer->capacity > 0 && $currentEnrollment >= $courseLecturer->capacity) {
            return back()->withErrors(['student_id' => 'This course section has reached its capacity.']);
        }

        CourseStudent::create([
            'student_id' => $data['student_id'],
            'course_lecturer_id' => $data['course_lecturer_id'],
        ]);

        return back()->with('success', 'Student enrolled successfully!');
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
}
