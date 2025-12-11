<?php

use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\LecturerController;
use Illuminate\Support\Facades\Route;

// Ensure root renders the welcome page
Route::get('/', function () {
    return view('index.gradely_welcome_page');
})->name('home');

// Simple page for login view (GET only)
Route::get('/login', function () {
    return view('index.login');
})->name('login');

// Add POST handler for registration form to persist user to database


// Protected Dashboard Routes (require authentication)
Route::middleware('auth')->group(function () {
        // Profile view and update
        Route::get('/profile', function () {
            return view('index.profile');
        })->name('profile.view');

        Route::post('/profile', function (\Illuminate\Http\Request $request) {
            $user = auth()->user();
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
            ]);
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
    // Student Dashboard
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    // Lecturer Dashboard
    Route::get('/dashboard/lecturer', function () {
        return response(view('lecturer.lecturer_dashboard'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('lecturer.dashboard');

    // Lecturer Courses
    Route::get('/lecturer/courses', [LecturerController::class, 'courses'])
        ->name('lecturer.courses');
    
    Route::get('/lecturer/course/{courseId}', [LecturerController::class, 'showCourse'])
        ->name('lecturer.course.show');

    // Lecturer Assignment Management
    Route::post('/lecturer/course/{courseId}/assignment', [LecturerController::class, 'storeAssignment'])
        ->name('lecturer.assignment.store');
    
    Route::put('/lecturer/course/{courseId}/assignment/{assignmentId}', [LecturerController::class, 'updateAssignment'])
        ->name('lecturer.assignment.update');
    
    Route::delete('/lecturer/course/{courseId}/assignment/{assignmentId}', [LecturerController::class, 'deleteAssignment'])
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

// Logout route
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('success', 'You have been logged out');
})->name('logout');
