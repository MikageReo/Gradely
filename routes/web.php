<?php

use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\AdminCourseController;
use Illuminate\Support\Facades\Route;

// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Simple page for login view (GET only)
Route::get('/login', function () {
    return view('index.login');
})->name('login');

// Add POST handler for registration form to persist user to database


// Protected Dashboard Routes (require authentication)
Route::middleware('auth')->group(function () {
    // Admin: Register Users (manual and bulk)
    Route::get('/admin/register-users', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return view('admin.register_users');
    })->name('admin.register_users');

    // Manual registration handler
    Route::post('/admin/register-users/manual', [\App\Http\Controllers\AdminUserController::class, 'store'])
        ->name('admin.register_users.manual');

    // Bulk registration handler (Excel)
    Route::post('/admin/register-users/bulk', [\App\Http\Controllers\AdminUserController::class, 'bulkRegister'])
        ->name('admin.register_users.bulk');
    // Profile view and update
    Route::get('/profile', function () {
        return view('index.profile');
    })->name('profile.view');

    Route::post('/profile', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Please add your full name.',
            'email.required' => 'Please add your email address.',
            'email.email' => 'Please enter a valid email address (e.g. example@gmail.com).',
            'email.unique' => 'This email is already taken.',
            'current_password.required' => 'Please enter your current password.',
            'password.min' => 'New password must be at least 8 characters.',
            'password.confirmed' => 'New password confirmation does not match.',
        ]);
        // Check current password
        if (!\Illuminate\Support\Facades\Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($data['password']);
        }
        $user->save();
        // Redirect to dashboard after update
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Profile updated successfully!');
        } elseif ($user->role === 'lecturer') {
            return redirect()->route('lecturer.dashboard')->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->route('student.dashboard')->with('success', 'Profile updated successfully!');
        }
    })->name('profile.update');
    // Admin: New Lecturer Registration Page
    Route::get('/admin/new-lecturer-registration', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return view('admin.new_lecturer_registration');
    })->name('admin.new_lecturer_registration');
    // Admin: New Student Registration Page
    Route::get('/admin/new-student-registration', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return view('admin.new_student_registration');
    })->name('admin.new_student_registration');
    // Admin: Register Student or Lecturer
    Route::get('/admin/create-user', [\App\Http\Controllers\AdminUserController::class, 'create'])
        ->name('admin.create_user');

    Route::post('/admin/store-user', [\App\Http\Controllers\AdminUserController::class, 'store'])
        ->name('admin.store_user');

    Route::post('/admin/bulk-register', [\App\Http\Controllers\AdminUserController::class, 'bulkRegister'])
        ->name('admin.bulk_register');

    Route::get('/admin/download-template', [\App\Http\Controllers\AdminUserController::class, 'downloadTemplate'])
        ->name('admin.download_template');
    // Admin Dashboard
    Route::get('/dashboard/admin', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return response(view('admin.admin_dashboard'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('admin.dashboard');

    // Admin Course Management
    Route::prefix('admin/courses')->name('admin.courses.')->group(function () {
        Route::get('/', [AdminCourseController::class, 'index'])->name('index');
        Route::get('/create', [AdminCourseController::class, 'create'])->name('create');
        Route::post('/', [AdminCourseController::class, 'store'])->name('store');
        Route::get('/{courseId}', [AdminCourseController::class, 'show'])->name('show');
        Route::get('/{courseId}/edit', [AdminCourseController::class, 'edit'])->name('edit');
        Route::put('/{courseId}', [AdminCourseController::class, 'update'])->name('update');
        Route::delete('/{courseId}', [AdminCourseController::class, 'destroy'])->name('destroy');

        // Lecturer Assignment
        Route::post('/{courseId}/assign-lecturer', [AdminCourseController::class, 'assignLecturer'])->name('assign.lecturer');
        Route::delete('/{courseId}/lecturer/{courseLecturerId}', [AdminCourseController::class, 'removeLecturer'])->name('remove.lecturer');

        // Student Enrollment
        Route::post('/{courseId}/enroll-student', [AdminCourseController::class, 'enrollStudent'])->name('enroll.student');
        Route::post('/{courseId}/bulk-enroll-student', [AdminCourseController::class, 'bulkEnrollStudent'])->name('bulk.enroll.student');
        Route::get('/{courseId}/download-enrollment-template', [AdminCourseController::class, 'downloadEnrollmentTemplate'])->name('download.enrollment_template');
        Route::delete('/{courseId}/student/{enrollmentId}', [AdminCourseController::class, 'removeStudent'])->name('remove.student');
    });
    // Student Dashboard
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    // Student Course Detail
    Route::get('/student/course/{courseId}', [StudentDashboardController::class, 'showCourse'])
        ->name('student.course.show');

    // Lecturer Dashboard
    Route::get('/dashboard/lecturer', [LecturerController::class, 'dashboard'])
        ->name('lecturer.dashboard');

    // Lecturer Courses
    Route::get('/lecturer/courses', [LecturerController::class, 'courses'])
        ->name('lecturer.courses');

    Route::get('/lecturer/course/{courseId}', [LecturerController::class, 'showCourse'])
        ->name('lecturer.course.show');

    Route::get('/lecturer/course/{courseId}/assignment/create', [LecturerController::class, 'createAssignment'])
        ->name('lecturer.assignment.create');

    Route::get('/lecturer/course/{courseId}/assignment/{assignmentId}/edit', [LecturerController::class, 'editAssignment'])
        ->name('lecturer.assignment.edit');

    // Lecturer Assignment Management (with rate limiting for capacity)
    Route::post('/lecturer/course/{courseId}/assignment', [LecturerController::class, 'storeAssignment'])
        ->middleware('throttle:30,1') // 30 requests per minute
        ->name('lecturer.assignment.store');

    Route::put('/lecturer/course/{courseId}/assignment/{assignmentId}', [LecturerController::class, 'updateAssignment'])
        ->middleware('throttle:30,1') // 30 requests per minute
        ->name('lecturer.assignment.update');

    Route::delete('/lecturer/course/{courseId}/assignment/{assignmentId}', [LecturerController::class, 'deleteAssignment'])
        ->middleware('throttle:20,1') // 20 requests per minute for delete operations
        ->name('lecturer.assignment.delete');

    Route::get('/lecturer/course/{courseId}/assignment/{assignmentId}/grading', [LecturerController::class, 'viewGrading'])
        ->name('lecturer.grading');

    // Assignment Submission Routes
    Route::get('/assignment/{assignmentId}/submission', [SubmissionController::class, 'show'])
        ->name('assignment.submission');
    Route::post('/assignment/{assignmentId}/submission', [SubmissionController::class, 'store'])
        ->name('assignment.submission.store');
    Route::post('/assignment/{assignmentId}/submission/comment', [SubmissionController::class, 'storeComment'])
        ->name('assignment.submission.comment');

    Route::post('/assignment/{assignmentId}/submission/grade', [SubmissionController::class, 'updateGrade'])
        ->name('assignment.submission.grade');

    // Protected File Download Routes
    Route::get('/assignment/{assignmentId}/attachment/download', [SubmissionController::class, 'downloadAssignmentAttachment'])
        ->name('assignment.attachment.download');
    Route::get('/submission/{submissionId}/file/{fileId}/download', [SubmissionController::class, 'downloadSubmissionFile'])
        ->name('submission.file.download');
});

// Add POST handler for login form
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
        'role' => 'required|in:student,lecturer,admin',
    ]);

    // Find user with matching email and role
    $user = \App\Models\User::where('email', $data['email'])
        ->where('role', $data['role'])
        ->first();

    // Check if user exists and password matches
    if (!$user || !\Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
        return back()
            ->withErrors(['email' => 'Invalid credentials for the selected role.'])
            ->withInput($request->only('email', 'role'));
    }

    \Illuminate\Support\Facades\Auth::login($user);

    // Redirect to role-specific dashboard
    if ($user->role === 'student') {
        return redirect('/dashboard/student')->with('success', 'Login successful');
    } elseif ($user->role === 'lecturer') {
        return redirect('/dashboard/lecturer')->with('success', 'Login successful');
    } else {
        return redirect('/dashboard/admin')->with('success', 'Login successful');
    }
})->name('login.post');

// Logout route - redirects directly to login page
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout');
