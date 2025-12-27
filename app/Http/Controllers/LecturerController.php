<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\Assignments;
use App\Models\Submissions;
use App\Models\CourseLecturer;
use App\Models\CourseStudent;
use App\Models\AssignmentFile;
use App\Models\User;
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

            // Get all course_lecturer_ids (sections) for this lecturer in this course
            $lecturerSectionIds = CourseLecturer::where('course_id', $courseId)
                ->where('lecturer_id', $user->id)
                ->pluck('id');

            // Build query with search and filter capabilities
            // Only show assignments from the lecturer's sections
            $query = Assignments::where('course_id', $courseId)
                ->whereIn('course_lecturer_id', $lecturerSectionIds);
            
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
            
            // Add pagination - limit to 3 assignments per page (Capacity improvement)
            $assignments = $query->withCount([
                'submissions',
                'submissions as pending_grading_count' => function ($query) {
                    $query->whereNull('score');
                }
            ])
                ->orderBy('created_at', 'desc')
                ->paginate(3)
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

            // Calculate total students from lecturer's sections only
            $totalStudents = CourseStudent::whereIn('course_lecturer_id', $lecturerSectionIds)
                ->distinct('student_id')
                ->count();

            // Get students enrolled in lecturer's sections only
            $enrolledStudents = User::whereHas('studentCourseSections', function($query) use ($lecturerSectionIds) {
                $query->whereIn('course_lecturer_id', $lecturerSectionIds);
            })
            ->orderBy('name')
            ->get();

            return view('lecturer.course_detail', [
                'course' => $course,
                'assignments' => $assignments,
                'totalStudents' => $totalStudents,
                'totalSubmissions' => $totalSubmissions,
                'pendingGrading' => $pendingGrading,
                'completed' => $completed,
                'enrolledStudents' => $enrolledStudents,
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

        // Get all course_lecturer_ids (sections) for this lecturer in this course
        $lecturerSectionIds = CourseLecturer::where('course_id', $courseId)
            ->where('lecturer_id', $user->id)
            ->pluck('id');

        $assignment = Assignments::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->where('lecturer_id', $user->id)
            ->whereIn('course_lecturer_id', $lecturerSectionIds)
            ->with('assignmentFiles')
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
            // Check for PHP upload errors before validation
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    if (!$file->isValid()) {
                        $errorCode = $file->getError();
                        if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
                            return back()->withErrors(['files' => 'File size exceeds the maximum allowed size. Each file cannot exceed 10MB.'])->withInput();
                        }
                        return back()->withErrors(['files' => 'File upload failed. Please try again.'])->withInput();
                    }
                }
                
                // Manual file size check (10MB = 10485760 bytes)
                foreach ($request->file('files') as $key => $file) {
                    $fileSize = $file->getSize();
                    if ($fileSize > 10485760) { // 10MB in bytes
                        return back()->withErrors(['files.' . $key => 'Each file size cannot exceed 10MB.'])->withInput();
                    }
                }
            }
            
            DB::beginTransaction();
            
            // Verify course belongs to lecturer through course_lecturer
            $course = Courses::where('id', $courseId)
                ->whereHas('courseLecturers', function($query) use ($user) {
                    $query->where('lecturer_id', $user->id);
                })
                ->firstOrFail();

            // Get the lecturer's course_lecturer_id (section) for this course
            // If lecturer teaches multiple sections, use the first one
            $courseLecturer = CourseLecturer::where('course_id', $courseId)
                ->where('lecturer_id', $user->id)
                ->firstOrFail();

            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000', // Add max length for capacity
                'due_date' => 'nullable|date|after:now', // Validate future date
                'status' => 'required|in:open,close',
                'visibility' => 'required|in:published,hidden',
                'files' => 'nullable|array',
                'files.*' => 'file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max per file
            ], [
                'title.required' => 'Assignment title is required.',
                'title.max' => 'Title cannot exceed 255 characters.',
                'description.max' => 'Description cannot exceed 5000 characters.',
                'due_date.after' => 'Due date must be in the future.',
                'files.*.max' => 'Each file size cannot exceed 10MB.',
                'files.*.file' => 'Invalid file type. Only PDF, DOC, DOCX, and TXT files are allowed.',
                'files.*.mimes' => 'Invalid file type. Only PDF, DOC, DOCX, and TXT files are allowed.',
            ]);

            $assignment = new Assignments();
            $assignment->course_id = $courseId;
            $assignment->lecturer_id = $user->id;
            $assignment->course_lecturer_id = $courseLecturer->id;
            $assignment->title = $data['title'];
            $assignment->description = $data['description'] ?? null;
            $assignment->due_date = $data['due_date'] ?? null;
            $assignment->status = $data['status'];
            $assignment->visibility = $data['visibility'];

            $assignment->save();

            // Handle multiple file uploads
            if ($request->hasFile('files')) {
                try {
                    $publicPath = public_path('assignments/' . $assignment->id);
                    
                    // Create directory if it doesn't exist
                    if (!File::exists($publicPath)) {
                        File::makeDirectory($publicPath, 0755, true);
                    }
                    
                    // Check disk space availability (Capacity improvement)
                    $freeSpace = disk_free_space($publicPath);
                    $totalFileSize = 0;
                    foreach ($request->file('files') as $file) {
                        $totalFileSize += $file->getSize();
                    }
                    if ($freeSpace !== false && $freeSpace < ($totalFileSize + 10485760)) { // Reserve 10MB buffer
                        throw new \Exception('Insufficient disk space. Please free up space and try again.');
                    }
                    
                    // Store each file
                    foreach ($request->file('files') as $file) {
                        $originalName = $file->getClientOriginalName();
                        $fileSize = $file->getSize();
                        $fileType = $file->getMimeType();
                        
                        // Use unique filename to prevent conflicts
                        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                        $uniqueName = $fileName . '_' . time() . '_' . uniqid() . '.' . $extension;
                        
                        $file->move($publicPath, $uniqueName);
                        $relativePath = 'assignments/' . $assignment->id . '/' . $uniqueName;
                        
                        AssignmentFile::create([
                            'assignment_id' => $assignment->id,
                            'file_path' => $relativePath,
                            'original_filename' => $originalName,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('File upload failed during assignment creation', [
                        'error' => $e->getMessage(),
                        'lecturer_id' => $user->id,
                        'course_id' => $courseId,
                    ]);
                    return back()->withErrors(['files' => 'File upload failed: ' . $e->getMessage()])->withInput();
                }
            }
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
            // Check for PHP upload errors before validation
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    if (!$file->isValid()) {
                        $errorCode = $file->getError();
                        if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
                            return back()->withErrors(['files' => 'File size exceeds the maximum allowed size. Each file cannot exceed 10MB.'])->withInput();
                        }
                        return back()->withErrors(['files' => 'File upload failed. Please try again.'])->withInput();
                    }
                }
                
                // Manual file size check (10MB = 10485760 bytes)
                foreach ($request->file('files') as $key => $file) {
                    $fileSize = $file->getSize();
                    if ($fileSize > 10485760) { // 10MB in bytes
                        return back()->withErrors(['files.' . $key => 'Each file size cannot exceed 10MB.'])->withInput();
                    }
                }
            }
            
            DB::beginTransaction();
            
            // Verify course belongs to lecturer through course_lecturer
            $course = Courses::where('id', $courseId)
                ->whereHas('courseLecturers', function($query) use ($user) {
                    $query->where('lecturer_id', $user->id);
                })
                ->firstOrFail();

            // Get all course_lecturer_ids (sections) for this lecturer in this course
            $lecturerSectionIds = CourseLecturer::where('course_id', $courseId)
                ->where('lecturer_id', $user->id)
                ->pluck('id');

            $assignment = Assignments::where('id', $assignmentId)
                ->where('course_id', $courseId)
                ->where('lecturer_id', $user->id)
                ->whereIn('course_lecturer_id', $lecturerSectionIds)
                ->firstOrFail();

            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000', // Add max length for capacity
                'due_date' => 'nullable|date',
                'status' => 'required|in:open,close',
                'visibility' => 'required|in:published,hidden',
                'files' => 'nullable|array',
                'files.*' => 'file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max per file
            ], [
                'title.required' => 'Assignment title is required.',
                'title.max' => 'Title cannot exceed 255 characters.',
                'description.max' => 'Description cannot exceed 5000 characters.',
                'files.*.max' => 'Each file size cannot exceed 10MB.',
                'files.*.file' => 'Invalid file type. Only PDF, DOC, DOCX, and TXT files are allowed.',
                'files.*.mimes' => 'Invalid file type. Only PDF, DOC, DOCX, and TXT files are allowed.',
            ]);

            $assignment->title = $data['title'];
            $assignment->description = $data['description'] ?? null;
            $assignment->due_date = $data['due_date'] ?? null;
            $assignment->status = $data['status'];
            $assignment->visibility = $data['visibility'];

            $assignment->save();

            // Handle multiple file uploads (only if new files are uploaded)
            if ($request->hasFile('files')) {
                try {
                    $publicPath = public_path('assignments/' . $assignment->id);
                    
                    // Create directory if it doesn't exist
                    if (!File::exists($publicPath)) {
                        File::makeDirectory($publicPath, 0755, true);
                    }
                    
                    // Check disk space availability (Capacity improvement)
                    $freeSpace = disk_free_space($publicPath);
                    $totalFileSize = 0;
                    foreach ($request->file('files') as $file) {
                        $totalFileSize += $file->getSize();
                    }
                    if ($freeSpace !== false && $freeSpace < ($totalFileSize + 10485760)) { // Reserve 10MB buffer
                        throw new \Exception('Insufficient disk space. Please free up space and try again.');
                    }
                    
                    // Delete old files if requested (when files are uploaded, old ones are replaced)
                    $oldFiles = AssignmentFile::where('assignment_id', $assignment->id)->get();
                    foreach ($oldFiles as $oldFile) {
                        $oldFilePath = public_path($oldFile->file_path);
                        if (File::exists($oldFilePath)) {
                            File::delete($oldFilePath);
                        }
                        $oldFile->delete();
                    }
                    
                    // Store new files
                    foreach ($request->file('files') as $file) {
                        $originalName = $file->getClientOriginalName();
                        $fileSize = $file->getSize();
                        $fileType = $file->getMimeType();
                        
                        // Use unique filename to prevent conflicts
                        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                        $uniqueName = $fileName . '_' . time() . '_' . uniqid() . '.' . $extension;
                        
                        $file->move($publicPath, $uniqueName);
                        $relativePath = 'assignments/' . $assignment->id . '/' . $uniqueName;
                        
                        AssignmentFile::create([
                            'assignment_id' => $assignment->id,
                            'file_path' => $relativePath,
                            'original_filename' => $originalName,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('File upload failed during assignment update', [
                        'error' => $e->getMessage(),
                        'assignment_id' => $assignmentId,
                        'lecturer_id' => $user->id,
                        'course_id' => $courseId,
                    ]);
                    return back()->withErrors(['files' => 'File upload failed: ' . $e->getMessage()])->withInput();
                }
            }
            // If no new files are uploaded, keep the existing files (do nothing)

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

            // Get all course_lecturer_ids (sections) for this lecturer in this course
            $lecturerSectionIds = CourseLecturer::where('course_id', $courseId)
                ->where('lecturer_id', $user->id)
                ->pluck('id');

            $assignment = Assignments::where('id', $assignmentId)
                ->where('course_id', $courseId)
                ->where('lecturer_id', $user->id)
                ->whereIn('course_lecturer_id', $lecturerSectionIds)
                ->firstOrFail();

            // Delete all assignment files
            $assignmentFiles = AssignmentFile::where('assignment_id', $assignmentId)->get();
            foreach ($assignmentFiles as $assignmentFile) {
                $filePath = public_path($assignmentFile->file_path);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $assignmentFile->delete();
            }
            
            // Delete assignment directory if exists
            $assignmentDir = public_path('assignments/' . $assignmentId);
            if (File::exists($assignmentDir)) {
                File::deleteDirectory($assignmentDir);
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

        // Get all course_lecturer_ids (sections) for this lecturer in this course
        $lecturerSectionIds = CourseLecturer::where('course_id', $courseId)
            ->where('lecturer_id', $user->id)
            ->pluck('id');

        $assignment = Assignments::where('id', $assignmentId)
            ->where('course_id', $courseId)
            ->whereIn('course_lecturer_id', $lecturerSectionIds)
            ->with('course')
            ->firstOrFail();

        // Only show submissions from students enrolled in the lecturer's sections
        $enrolledStudentIds = CourseStudent::whereIn('course_lecturer_id', $lecturerSectionIds)
            ->pluck('student_id')
            ->unique();
        
        // Get submissions only from students in the lecturer's sections
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

