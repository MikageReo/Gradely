<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\Assignments;
use App\Models\Submissions;
use App\Models\CourseLecturer;
use App\Models\CourseStudent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public function showCourse($courseId, Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'lecturer') {
            abort(403, 'Unauthorized');
        }

        try {
            // Verify course belongs to lecturer through course_lecturer
            $course = Courses::where('id', $courseId)
                ->whereHas('courseLecturers', function($query) use ($user) {
                    $query->where('lecturer_id', $user->id);
                })
                ->with(['courseLecturers.students.student'])
                ->firstOrFail();

            // Build query with search and filter capabilities
            $query = Assignments::where('course_id', $courseId);
            
            // Search filter
            if ($request->has('search') && $request->search) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }
            
            // Status filter
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Visibility filter
            if ($request->has('visibility') && $request->visibility) {
                $query->where('visibility', $request->visibility);
            }
            
            // Add pagination - limit to 5 assignments per page (Capacity improvement)
            $assignments = $query->withCount('submissions')
                ->orderBy('created_at', 'desc')
                ->paginate(5)
                ->withQueryString(); // Preserve query parameters in pagination links

            // Optimize analytics queries using assignment IDs from paginated results
            $assignmentIds = $assignments->pluck('id');
            
            if ($assignmentIds->isNotEmpty()) {
                $totalSubmissions = Submissions::whereIn('assignment_id', $assignmentIds)->count();
                $pendingGrading = Submissions::whereIn('assignment_id', $assignmentIds)
                    ->whereNull('score')
                    ->count();
                $completed = Submissions::whereIn('assignment_id', $assignmentIds)
                    ->whereNotNull('score')
                    ->count();
            } else {
                $totalSubmissions = 0;
                $pendingGrading = 0;
                $completed = 0;
            }

            // Calculate total students more efficiently
            $totalStudents = CourseStudent::whereHas('courseLecturer', function($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })->distinct('student_id')->count();

            return view('lecturer.course_detail', [
                'course' => $course,
                'assignments' => $assignments,
                'totalStudents' => $totalStudents,
                'totalSubmissions' => $totalSubmissions,
                'pendingGrading' => $pendingGrading,
                'completed' => $completed,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading course detail', [
                'error' => $e->getMessage(),
                'course_id' => $courseId,
                'lecturer_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('lecturer.dashboard')
                ->withErrors(['error' => 'Failed to load course details. Please try again.']);
        }
    }

    /**
     * Show create assignment page
     */
    public function createAssignment($courseId)
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

        return view('lecturer.assignment_form', [
            'course' => $course,
            'assignment' => null,
            'mode' => 'create',
        ]);
    }

    /**
     * Show edit assignment page
     */
    public function editAssignment($courseId, $assignmentId)
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

        return view('lecturer.assignment_form', [
            'course' => $course,
            'assignment' => $assignment,
            'mode' => 'edit',
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

        try {
            DB::beginTransaction();
            
            // Verify course belongs to lecturer through course_lecturer
            $course = Courses::where('id', $courseId)
                ->whereHas('courseLecturers', function($query) use ($user) {
                    $query->where('lecturer_id', $user->id);
                })
                ->firstOrFail();

            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000', // Add max length for capacity
                'due_date' => 'nullable|date|after:now', // Validate future date
                'status' => 'required|in:open,close',
                'visibility' => 'required|in:published,hidden',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            ], [
                'title.required' => 'Assignment title is required.',
                'title.max' => 'Title cannot exceed 255 characters.',
                'description.max' => 'Description cannot exceed 5000 characters.',
                'due_date.after' => 'Due date must be in the future.',
                'attachment.max' => 'Attachment size cannot exceed 10MB.',
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
                try {
                    $file = $request->file('attachment');
                    $originalName = $file->getClientOriginalName();
                    $publicPath = public_path('assignments');
                    
                    // Check disk space availability (Capacity improvement)
                    $freeSpace = disk_free_space($publicPath);
                    $fileSize = $file->getSize();
                    if ($freeSpace !== false && $freeSpace < ($fileSize + 10485760)) { // Reserve 10MB buffer
                        throw new \Exception('Insufficient disk space. Please free up space and try again.');
                    }
                    
                    // Create directory if it doesn't exist
                    if (!File::exists($publicPath)) {
                        File::makeDirectory($publicPath, 0755, true);
                    }
                    
                    // Use unique filename to prevent conflicts
                    $uniqueName = time() . '_' . uniqid() . '_' . $originalName;
                    $file->move($publicPath, $uniqueName);
                    $assignment->attachment = 'assignments/' . $uniqueName;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('File upload failed during assignment creation', [
                        'error' => $e->getMessage(),
                        'lecturer_id' => $user->id,
                        'course_id' => $courseId,
                    ]);
                    return back()->withErrors(['attachment' => 'File upload failed: ' . $e->getMessage()])->withInput();
                }
            }

            $assignment->save();
            DB::commit();
            
            // Log successful creation (Availability improvement)
            Log::info('Assignment created successfully', [
                'assignment_id' => $assignment->id,
                'lecturer_id' => $user->id,
                'course_id' => $courseId,
                'title' => $assignment->title,
            ]);

            return redirect()->route('lecturer.course.show', $courseId)
                ->with('success', 'Assignment created successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assignment creation failed', [
                'error' => $e->getMessage(),
                'lecturer_id' => $user->id,
                'course_id' => $courseId,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to create assignment. Please try again.'])->withInput();
        }
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

        try {
            DB::beginTransaction();
            
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
                'description' => 'nullable|string|max:5000', // Add max length for capacity
                'due_date' => 'nullable|date',
                'status' => 'required|in:open,close',
                'visibility' => 'required|in:published,hidden',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            ], [
                'title.required' => 'Assignment title is required.',
                'title.max' => 'Title cannot exceed 255 characters.',
                'description.max' => 'Description cannot exceed 5000 characters.',
                'attachment.max' => 'Attachment size cannot exceed 10MB.',
            ]);

            $assignment->title = $data['title'];
            $assignment->description = $data['description'] ?? null;
            $assignment->due_date = $data['due_date'] ?? null;
            $assignment->status = $data['status'];
            $assignment->visibility = $data['visibility'];

            // Handle attachment update
            if ($request->hasFile('attachment')) {
                try {
                    // Delete old attachment if exists
                    if ($assignment->attachment) {
                        $oldFilePath = public_path($assignment->attachment);
                        if (File::exists($oldFilePath)) {
                            File::delete($oldFilePath);
                        }
                    }
                    
                    // Store new attachment
                    $file = $request->file('attachment');
                    $originalName = $file->getClientOriginalName();
                    $publicPath = public_path('assignments');
                    
                    // Check disk space availability (Capacity improvement)
                    $freeSpace = disk_free_space($publicPath);
                    $fileSize = $file->getSize();
                    if ($freeSpace !== false && $freeSpace < ($fileSize + 10485760)) { // Reserve 10MB buffer
                        throw new \Exception('Insufficient disk space. Please free up space and try again.');
                    }
                    
                    // Create directory if it doesn't exist
                    if (!File::exists($publicPath)) {
                        File::makeDirectory($publicPath, 0755, true);
                    }
                    
                    // Use unique filename to prevent conflicts
                    $uniqueName = time() . '_' . uniqid() . '_' . $originalName;
                    $file->move($publicPath, $uniqueName);
                    $assignment->attachment = 'assignments/' . $uniqueName;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('File upload failed during assignment update', [
                        'error' => $e->getMessage(),
                        'assignment_id' => $assignmentId,
                        'lecturer_id' => $user->id,
                        'course_id' => $courseId,
                    ]);
                    return back()->withErrors(['attachment' => 'File upload failed: ' . $e->getMessage()])->withInput();
                }
            }
            // If no new file is uploaded, keep the existing attachment (do nothing)

            $assignment->save();
            DB::commit();
            
            // Log successful update (Availability improvement)
            Log::info('Assignment updated successfully', [
                'assignment_id' => $assignment->id,
                'lecturer_id' => $user->id,
                'course_id' => $courseId,
            ]);

            return redirect()->route('lecturer.course.show', $courseId)
                ->with('success', 'Assignment updated successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assignment update failed', [
                'error' => $e->getMessage(),
                'assignment_id' => $assignmentId,
                'lecturer_id' => $user->id,
                'course_id' => $courseId,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to update assignment. Please try again.'])->withInput();
        }
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

        try {
            DB::beginTransaction();
            
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
            DB::commit();
            
            // Log successful deletion (Availability improvement)
            Log::info('Assignment deleted successfully', [
                'assignment_id' => $assignmentId,
                'lecturer_id' => $user->id,
                'course_id' => $courseId,
            ]);

            return redirect()->route('lecturer.course.show', $courseId)
                ->with('success', 'Assignment deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assignment deletion failed', [
                'error' => $e->getMessage(),
                'assignment_id' => $assignmentId,
                'lecturer_id' => $user->id,
                'course_id' => $courseId,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('lecturer.course.show', $courseId)
                ->withErrors(['error' => 'Failed to delete assignment. Please try again.']);
        }
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
            ->with('course')
            ->firstOrFail();

        // Verify lecturer is assigned to this course or is the assignment creator
        $isAssignedLecturer = CourseLecturer::where('course_id', $courseId)
            ->where('lecturer_id', $user->id)
            ->exists();
        
        if (!$isAssignedLecturer && $assignment->lecturer_id !== $user->id) {
            abort(403, 'You are not authorized to view submissions for this assignment.');
        }

        // Only show submissions from students enrolled in this course
        // Get all course_lecturer IDs for this course
        $courseLecturerIds = CourseLecturer::where('course_id', $courseId)->pluck('id');
        
        // Get all student IDs enrolled in this course
        $enrolledStudentIds = CourseStudent::whereIn('course_lecturer_id', $courseLecturerIds)
            ->pluck('student_id')
            ->unique();
        
        // Get submissions only from enrolled students
        $submissions = Submissions::where('assignment_id', $assignmentId)
            ->whereIn('student_id', $enrolledStudentIds)
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

